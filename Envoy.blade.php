@setup
    if(file_exists(__DIR__.'/.env')) {
        require __DIR__.'/vendor/autoload.php';
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
        try {
            $dotenv->load();
            $dotenv->required(['DEPLOY_SERVER', 'DEPLOY_REPOSITORY', 'DEPLOY_PATH'])->notEmpty();
        } catch ( Exception $e )  {
            echo $e->getMessage();
            exit;
        }
    }

    $server = $_ENV['DEPLOY_SERVER'] ?? null;
    $repo = $_ENV['DEPLOY_REPOSITORY'] ?? null;
    $path = $_ENV['DEPLOY_PATH'] ?? null;
    $telegramBot = $_ENV['DEPLOY_TELEGRAM_BOT'] ?? null;
    $telegramChat = $_ENV['DEPLOY_TELEGRAM_CHAT'] ?? null;
    $healthUrl = $_ENV['DEPLOY_HEALTH_CHECK'] ?? null;

    if ( substr($path, 0, 1) !== '/' ) throw new Exception('Careful - your deployment path does not begin with /');

    $date = ( new DateTime )->format('YmdHis');
    $env = isset($env) ? $env : "production";
    $branch = isset($branch) ? $branch : "master";
    $path = rtrim($path, '/');
    $release = $path.'/'.$date;
@endsetup

@servers(['web' => $server])

@story('deploy')
    deployment_start
    deployment_npm
    deployment_finish
    health_check
    deployment_option_cleanup
@endstory

@story('deploy_cleanup')
    deployment_start
    deployment_npm
    deployment_finish
    health_check
    deployment_cleanup
@endstory

@story('rollback')
    deployment_rollback
    health_check
@endstory

@task('deployment_start')
    cd {{ $path }}
    echo "Deployment ({{ $date }}) started"
    git clone {{ $repo }} --branch={{ $branch }} --depth=1 -q {{ $release }}
    echo "Repository cloned"
@endtask

@task('deployment_npm')
    echo "Installing npm dependencies..."
    cd {{ $release }}
    npm ci --loglevel warn
    npm run build
@endtask

@task('deployment_finish')
    ln -nfs {{ $release }} {{ $path }}/current
    echo "Deployment ({{ $date }}) finished"
@endtask

@task('deployment_cleanup')
    cd {{ $path }}
    find . -maxdepth 1 -name "20*" | sort | head -n -4 | xargs rm -Rf
    echo "Cleaned up old deployments"
@endtask

@task('deployment_option_cleanup')
    cd {{ $path }}
    @if ( isset($cleanup) && $cleanup )
        find . -maxdepth 1 -name "20*" | sort | head -n -4 | xargs rm -Rf
        echo "Cleaned up old deployments"
    @endif
@endtask


@task('health_check')
    @if ( ! empty($healthUrl) )
        if [ "$(curl --write-out "%{http_code}\n" --silent --output /dev/null {{ $healthUrl }})" == "200" ]; then
        printf "\033[0;32mHealth check to {{ $healthUrl }} OK\033[0m\n"
        else
        printf "\033[1;31mHealth check to {{ $healthUrl }} FAILED\033[0m\n"
        fi
    @else
        echo "No health check set"
    @endif
@endtask


@task('deployment_rollback')
    cd {{ $path }}
    ln -nfs {{ $path }}/$(find . -maxdepth 1 -name "20*" | sort  | tail -n 2 | head -n1) {{ $path }}/current
    echo "Rolled back to $(find . -maxdepth 1 -name "20*" | sort  | tail -n 2 | head -n1)"
@endtask

@finished
	@telegram($telegramBot, $telegramChat)
@endfinished

