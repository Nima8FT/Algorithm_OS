<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CpuController extends Controller
{

    public function fcfs(Request $request)
    {
        if ($request->input('Algorithm') == "FCFS") {
            $arrival_array = explode(' ', $request->get("Arrival"));
            $burst_array = explode(' ', $request->get("Burst"));

            for ($i = 0; $i < count($arrival_array); $i++) {
                $processes[$i] = ["P" . $i + 1, $arrival_array[$i], $burst_array[$i]];
            }

            usort($processes, function ($a, $b) {
                return $a[1] <=> $b[1];
            });

            $current = 0;
            $wating = [];
            $finish = [];
            $turnaround = [];
            $chart = [];

            // Process => [Proccess => P1, Arrival Times => 2 , Burst Times => 4 , Finish Time => 5, Turnaround => 2, Waiting Time => 3]

            for ($i = 0; $i < count($processes); $i++) {
                $arrival = $processes[$i][1];
                $burst = $processes[$i][2];
                if ($current < $arrival) {
                    array_push($chart, [$current, $arrival, "gap"]);
                    $current = $arrival;
                }
                array_push($finish, $burst + $current);
                array_push($turnaround, $finish[$i] - $arrival);
                array_push($wating, $turnaround[$i] - $burst);
                array_push($chart, [$current, $finish[$i], $processes[$i][0]]);
                $current = $finish[$i];
            }

            for ($i = 0; $i < count($processes); $i++) {
                array_push($processes[$i], $finish[$i], $turnaround[$i], $wating[$i]);
            }

            $col_turnaround = collect($turnaround);
            $avg_turnaround = $col_turnaround->avg();

            $col_waiting = collect($wating);
            $avg_waiting = $col_waiting->avg();

            return response()->json(
                [
                    "Process" => $processes,
                    "avg_turnaround" => $avg_turnaround,
                    "avg_waiting" => $avg_waiting,
                    "chart" => $chart
                ]
            );
        }
    }
}