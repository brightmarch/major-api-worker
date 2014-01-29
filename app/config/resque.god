MAJORAPI_ROOT = File.expand_path('../../', File.dirname(__FILE__))
WORKERS = 4

God.watch do |w|
    w.dir      = "#{MAJORAPI_ROOT}"
    w.name     = "resque-worker"
    w.group    = "resque"
    w.uid      = "deploy"
    w.gid      = "deploy"
    w.interval = 5.seconds
    w.log      = "#{MAJORAPI_ROOT}/log/resque.god.log"
    w.env      = { "QUEUE" => "ipp-requests,qbxml", "INTERVAL" => 5, "APP_INCLUDE" => "#{MAJORAPI_ROOT}/app/connect.php" }
    w.start    = "/usr/local/bin/php #{MAJORAPI_ROOT}/vendor/bin/resque"

    w.keepalive(
        :memory_max => 256.megabytes
    )
end
