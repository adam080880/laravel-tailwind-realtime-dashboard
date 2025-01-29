<?php

namespace Database\Seeders;

use App\Models\StreamData;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StreamDataSample extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {
            $fileCsv = fopen('./sample_data_snapshot.csv', 'r') or die('Unable to open file');
            $whitelistRawCsvString = [];
    
            while (!feof($fileCsv)) {
                $whitelistRawCsvString[] = fgets($fileCsv);
            }
    
            fclose($fileCsv);
            array_shift($whitelistRawCsvString);
    
            $newStreamData = [];
    
            foreach ($whitelistRawCsvString as $whitelistRawCsv) {
                $streamData = str_getcsv($whitelistRawCsv, ',');

                try {
                    $newStreamData[] = [
                        'id' => $streamData[0],
                        'pointName' => $streamData[1],
                        'pointValue' => $streamData[2] ? (double) preg_replace('/\.(?!\d)/', '', $streamData[2]) : 0,
                        'pointQuality' => $streamData[3],
                        'pointTimestamp' => !!$streamData[4] ? date_format(date_create($streamData[4]), 'Y-m-d H:i:s') : null,
                        'idPointAlias' => $streamData[5],
                        'insertTimestamp' => !!$streamData[6] ? date_format(date_create($streamData[6]), 'Y-m-d H:i:s') : null,
                    ];
                } catch (\Exception $e) {
                }
            }
    
            foreach (array_chunk($newStreamData, 100) as $chunk) {
                DB::table('stream_data')->insert($chunk);
            }
        });
    }
}
