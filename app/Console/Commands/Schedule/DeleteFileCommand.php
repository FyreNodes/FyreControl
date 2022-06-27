<?php

namespace Pterodactyl\Console\Commands\Schedule;

use Carbon\Carbon;
use Pterodactyl\Models\Server;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Pterodactyl\Repositories\Wings\DaemonFileRepository;
use Pterodactyl\Exceptions\Http\Connection\DaemonConnectionException;

class DeleteFileCommand extends Command
{
    /**
     * @var string
     */
    protected $description = 'Delete added files {fileId}';

    /**
     * @var string
     */
    protected $signature = 'p:schedule:deletefile {fileId}';

    /**
     * @var \Pterodactyl\Repositories\Wings\DaemonFileRepository
     */
    protected $daemonFileRepository;

    /**
     * DeleteFileCommand constructor.
     * @param DaemonFileRepository $daemonFileRepository
     */
    public function __construct(DaemonFileRepository $daemonFileRepository)
    {
        parent::__construct();

        $this->daemonFileRepository = $daemonFileRepository;
    }

    /**
     * Handle command execution.
     */
    public function handle()
    {
        $fileId = $this->argument('fileId');

        $file = DB::table('delete_files')->where('id', '=', $fileId)->get();
        if (count($file) < 1) {
            return;
        }

        $server = DB::table('servers')->where('id', '=', $file[0]->server_id)->get();
        if (count($server) < 1) {
            return;
        }

        try {
            $this->daemonFileRepository->setServer(Server::find($server[0]->id))->deleteFiles('/', [
                $file[0]->file,
            ]);
        } catch (DaemonConnectionException $e) {
            return;
        }

        DB::table('delete_files')->where('id', '=', $file[0]->id)->update([
            'last_deleted' => Carbon::now(),
        ]);
    }
}
