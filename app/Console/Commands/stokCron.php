<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Payment;
use Carbon\Carbon;

class stokCron extends Command
{
    protected $signature = 'cron:log';

    protected $description = 'Membuat log, yang memastikan command jalan';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $pembayaran = Payment::where('payment_status', 'unpaid')->get();
        $tanggalMulai = []; 
        $now = Carbon::now('+7:00'); //+7:00 adalah gmt nya

        foreach ($pembayaran as $p) {
                $selisih = $now->diffInDays($p->created_at);
                if ($selisih > 0 ) {
                    array_push($tanggalMulai, $selisih);
                }
        }
        
        \Log::info($tanggalMulai);
        \Log::info($now);
    }
}
