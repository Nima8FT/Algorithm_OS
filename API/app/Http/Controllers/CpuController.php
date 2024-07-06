<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;

class CpuController extends Controller
{

    public function fcfs(Request $request)
    {
        if ($request->input('Algorithm') == "FCFS") {
            $arrival_array = explode(' ', $request->get("Arrival"));
            $burst_array = explode(' ', $request->get("Burst"));

            for ($i = 0; $i < count($arrival_array); $i++) {
                $processes[$i] = [
                    'process' => "P" . ($i + 1),
                    'arrival_time' => $arrival_array[$i],
                    'burst_time' => $burst_array[$i]
                ];
            }
            usort($processes, function ($a, $b) {
                return $a['arrival_time'] <=> $b['arrival_time'];
            });

            $current_time = 0;
            $finish_time = [];
            $wating_time = [];
            $turnaround_time = [];
            $gantt_chart = [];


            for ($i = 0; $i < count($processes); $i++) {
                $arrival = $processes[$i]['arrival_time'];
                $burst = $processes[$i]['burst_time'];
                if ($current_time < $arrival) {
                    $gantt_chart[] = [
                        "process" => "gap",
                        "start" => $current_time,
                        "end" => $arrival,
                    ];
                    $current_time = $arrival;
                }
                $finish_time[$i] = $current_time + $burst;
                $turnaround_time[$i] = $finish_time[$i] - $arrival;
                $wating_time[$i] = $turnaround_time[$i] - $burst;
                $gantt_chart[] = [
                    "process" => $processes[$i]['process'],
                    "start" => $current_time,
                    "end" => $finish_time[$i],
                ];
                $current_time = $finish_time[$i];
            }

            for ($i = 0; $i < count($processes); $i++) {
                array_push($processes[$i], [
                    "finish_time" => $finish_time[$i],
                    "turnaround_time" => $turnaround_time[$i],
                    "waiting_time" => $wating_time[$i],
                ]);
                $processes[$i] = array_merge($processes[$i], $processes[$i][0]);
                unset($processes[$i][0]);
            }

            $col_turnaround = collect($turnaround_time);
            $avg_turnaround = $col_turnaround->avg();

            $col_waiting = collect($wating_time);
            $avg_waiting = $col_waiting->avg();


            return response()->json(
                [
                    "Process" => $processes,
                    "avg_turnaround" => $avg_turnaround,
                    "avg_waiting" => $avg_waiting,
                    "chart" => $gantt_chart
                ]
            );
        }
    }

    public function sjf(Request $request)
    {
        if ($request->input('Algorithm') == "SJF") {
            $arrival_array = explode(' ', $request->get("Arrival"));
            $burst_array = explode(' ', $request->get("Burst"));

            for ($i = 0; $i < count($arrival_array); $i++) {
                $processes[$i] = [
                    'process' => "P" . ($i + 1),
                    'arrival_time' => $arrival_array[$i],
                    'burst_time' => $burst_array[$i]
                ];
            }

            usort($processes, function ($a, $b) {
                return $a['arrival_time'] <=> $b['arrival_time'];
            });

            $j = 0;
            $current_time = 0;
            $finish_time = [];
            $turnaround_time = [];
            $waiting_time = [];
            $gantt_chart = [];
            $is_process_complete = array_fill(0, count($processes), false);
            $remaining_burst_time = array_column($processes, 'burst_time');

            while ($j < count($processes)) {
                $shortest_process_index = -1;
                $shortest_process_burst = PHP_INT_MAX;
                
                for ($i = 0; $i < count($processes); $i++) {
                    if ($processes[$i]['arrival_time'] <= $current_time && !$is_process_complete[$i] && $remaining_burst_time[$i] < $shortest_process_burst) {
                        $shortest_process_burst = $remaining_burst_time[$i];
                        $shortest_process_index = $i;
                    }
                }
                
                if ($shortest_process_index == -1) {
                    $gantt_chart[] = [
                        'process' => 'gap',
                        'start' => $current_time,
                        'end' => $current_time + 1,
                    ];
                    $current_time++;
                } else {
                    $gantt_chart[] = [
                        'process' => $processes[$shortest_process_index]['process'],
                        'start' => $current_time,
                        'end' => $current_time + $remaining_burst_time[$shortest_process_index],
                    ];
                    
                    $current_time += $remaining_burst_time[$shortest_process_index];
                    $remaining_burst_time[$shortest_process_index] = 0;
                    $is_process_complete[$shortest_process_index] = true;
                    $finish_time[$shortest_process_index] = $current_time;
                    $turnaround_time[$shortest_process_index] = $finish_time[$shortest_process_index] - $processes[$shortest_process_index]['arrival_time'];
                    $waiting_time[$shortest_process_index] = $turnaround_time[$shortest_process_index] - $processes[$shortest_process_index]['burst_time'];
                    $j++;
                }
            }

            for ($i = 0; $i < count($processes); $i++) {
                array_push($processes[$i], [
                    "finish_time" => $finish_time[$i],
                    "turnaround_time" => $turnaround_time[$i],
                    "waiting_time" => $waiting_time[$i],
                ]);
                $processes[$i] = array_merge($processes[$i], $processes[$i][0]);
                unset($processes[$i][0]);
            }

            $col_turnaround = collect($turnaround_time);
            $avg_turnaround = $col_turnaround->avg();

            $col_waiting = collect($waiting_time);
            $avg_waiting = $col_waiting->avg();

            usort($processes, function ($a, $b) {
                return $a['process'] <=> $b['process'];
            });

            return response()->json(
                [
                    "Process" => $processes,
                    "avg_turnaround" => $avg_turnaround,
                    "avg_waiting" => $avg_waiting,
                    "chart" => $gantt_chart
                ]
            );
        }
    }
}