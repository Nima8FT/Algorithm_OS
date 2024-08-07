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
            $response_time = [];
            $gantt_chart = [];


            for ($i = 0; $i < count($processes); $i++) {
                $arrival = $processes[$i]['arrival_time'];
                $burst = $processes[$i]['burst_time'];
                if ($current_time < $arrival) {
                    $gantt_chart[] = [
                        "process" => "-",
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
                $response_time[$i] = $current_time - $arrival;
                $current_time = $finish_time[$i];
            }

            for ($i = 0; $i < count($processes); $i++) {
                array_push($processes[$i], [
                    "finish_time" => $finish_time[$i],
                    "turnaround_time" => $turnaround_time[$i],
                    "waiting_time" => $wating_time[$i],
                    "response_time" => $response_time[$i],
                ]);
                $processes[$i] = array_merge($processes[$i], $processes[$i][0]);
                unset($processes[$i][0]);
            }

            $col_turnaround = collect($turnaround_time);
            $avg_turnaround = $col_turnaround->avg();

            $col_waiting = collect($wating_time);
            $avg_waiting = $col_waiting->avg();

            $col_response = collect($response_time);
            $avg_response = $col_response->avg();

            usort($processes, function ($a, $b) {
                return $a['process'] <=> $b['process'];
            });

            return response()->json(
                [
                    "Process" => $processes,
                    "avg_turnaround" => substr($avg_turnaround, 0, 5),
                    "avg_waiting" => substr($avg_waiting, 0, 5),
                    "avg_response" => substr($avg_response, 0, 5),
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
            $response_time = [];
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
                        'process' => '-',
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

                    $response_time[$shortest_process_index] = $current_time - $processes[$shortest_process_index]['arrival_time'];
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
                    "response_time" => $response_time[$i],
                ]);
                $processes[$i] = array_merge($processes[$i], $processes[$i][0]);
                unset($processes[$i][0]);
            }

            $col_turnaround = collect($turnaround_time);
            $avg_turnaround = $col_turnaround->avg();

            $col_waiting = collect($waiting_time);
            $avg_waiting = $col_waiting->avg();

            $col_response = collect($response_time);
            $avg_response = $col_response->avg();

            usort($processes, function ($a, $b) {
                return $a['process'] <=> $b['process'];
            });

            return response()->json(
                [
                    "Process" => $processes,
                    "avg_turnaround" => substr($avg_turnaround, 0, 5),
                    "avg_waiting" => substr($avg_waiting, 0, 5),
                    "avg_response" => substr($avg_response, 0, 5),
                    "chart" => $gantt_chart
                ]
            );
        }
    }

    public function ljf(Request $request)
    {
        if ($request->input('Algorithm') == "LJF") {
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
            $response_time = [];
            $gantt_chart = [];
            $remaining_burst_time = array_column($processes, 'burst_time');
            $is_process_complete = array_fill(0, count($processes), false);

            while ($j < count($processes)) {
                $longest_process_index = -1;
                $longest_process_burst = 0;

                for ($i = 0; $i < count($processes); $i++) {
                    if ($processes[$i]['arrival_time'] <= $current_time && !$is_process_complete[$i] && $remaining_burst_time[$i] > $longest_process_burst) {
                        $longest_process_burst = $remaining_burst_time[$i];
                        $longest_process_index = $i;
                    }
                }

                if ($longest_process_index == -1) {
                    $gantt_chart[] = [
                        "process" => "-",
                        "start" => $current_time,
                        "end" => $current_time + 1,
                    ];
                    $current_time++;
                    continue;
                } else {
                    $gantt_chart[] = [
                        "process" => $processes[$longest_process_index]['process'],
                        "start" => $current_time,
                        "end" => $current_time + $remaining_burst_time[$longest_process_index],
                    ];

                    $response_time[$longest_process_index] = $current_time - $processes[$longest_process_index]["arrival_time"];
                    $current_time += $remaining_burst_time[$longest_process_index];
                    $remaining_burst_time[$longest_process_index] = 0;
                    $is_process_complete[$longest_process_index] = true;
                    $finish_time[$longest_process_index] = $current_time;
                    $turnaround_time[$longest_process_index] = $finish_time[$longest_process_index] - $processes[$longest_process_index]["arrival_time"];
                    $waiting_time[$longest_process_index] = $turnaround_time[$longest_process_index] - $processes[$longest_process_index]["burst_time"];
                    $j++;
                }
            }

            for ($i = 0; $i < count($processes); $i++) {
                array_push($processes[$i], [
                    "finish_time" => $finish_time[$i],
                    "turnaround_time" => $turnaround_time[$i],
                    "waiting_time" => $waiting_time[$i],
                    "response_time" => $response_time[$i],
                ]);
                $processes[$i] = array_merge($processes[$i], $processes[$i][0]);
                unset($processes[$i][0]);
            }

            $col_turnaround = collect($turnaround_time);
            $avg_turnaround = $col_turnaround->avg();

            $col_waiting = collect($waiting_time);
            $avg_waiting = $col_waiting->avg();

            $col_response = collect($response_time);
            $avg_response = $col_response->avg();


            usort($processes, function ($a, $b) {
                return $a['process'] <=> $b['process'];
            });

            return response()->json(
                [
                    "Process" => $processes,
                    "avg_turnaround" => substr($avg_turnaround, 0, 5),
                    "avg_waiting" => substr($avg_waiting, 0, 5),
                    "avg_response" => substr($avg_response, 0, 5),
                    "chart" => $gantt_chart
                ]
            );
        }
    }

    public function rr(Request $request)
    {
        if ($request->input("Algorithm") == "RR") {
            $arrival_array = explode(' ', $request->get("Arrival"));
            $burst_array = explode(' ', $request->get('Burst'));
            $quantom_time = $request->get('Quantom');

            for ($i = 0; $i < count($arrival_array); $i++) {
                $processes[$i] = [
                    "process" => "P" . $i + 1,
                    "arrival_time" => $arrival_array[$i],
                    "burst_time" => $burst_array[$i],
                ];
            }

            usort($processes, function ($a, $b) {
                return $a['arrival_time'] <=> $b['arrival_time'];
            });

            $j = 0;
            $current_time = 0;
            $finish_time = [];
            $turnaround_time = [];
            $waitng_time = [];
            $response_time = array_fill(0, count($processes), 0);
            $request_queue = [$processes[0]['process']];
            $remaining_burst_time = array_column($processes, 'burst_time');
            $is_process_complete = array_fill(0, count($processes), false);
            $is_process_start = array_fill(0, count($processes), false);
            $gantt_chart = [];

            function process_request(&$i, &$j, &$processes, $quantom_time, &$current_time, &$remaining_burst_time, &$is_process_complete, &$is_process_start, &$finish_time, &$request_queue, &$turnaround_time, &$waitng_time, &$gantt_chart, &$response_time)
            {
                $start_time = $current_time;

                if (!$is_process_start[$i]) {
                    $response_time[$i] = $start_time - $processes[$i]["arrival_time"];
                    $is_process_start[$i] = true;
                }

                if ($remaining_burst_time[$i] <= $quantom_time) {
                    $current_time += $remaining_burst_time[$i];
                    $remaining_burst_time[$i] = 0;
                    $j++;
                    $is_process_complete[$i] = true;
                    $finish_time[$i] = $current_time;
                    $turnaround_time[$i] = $finish_time[$i] - $processes[$i]["arrival_time"];
                    $waitng_time[$i] = $turnaround_time[$i] - $processes[$i]["burst_time"];
                    for ($k = 0; $k < count($processes); $k++) {
                        if ($processes[$k]["arrival_time"] <= $current_time && !$is_process_complete[$k] && $processes[$i]["process"] !== $processes[$k]["process"] && !in_array($processes[$k]["process"], $request_queue)) {
                            array_push($request_queue, $processes[$k]["process"]);
                        }
                    }
                } else {
                    $remaining_burst_time[$i] -= $quantom_time;
                    $current_time += $quantom_time;
                    for ($k = 0; $k < count($processes); $k++) {
                        if ($processes[$k]["arrival_time"] <= $current_time && !$is_process_complete[$k] && $processes[$i]["process"] !== $processes[$k]["process"] && !in_array($processes[$k]["process"], $request_queue)) {
                            array_push($request_queue, $processes[$k]["process"]);
                        }
                    }
                    array_push($request_queue, $processes[$i]["process"]);
                }


                $gantt_chart[] = [
                    "process" => $processes[$i]["process"],
                    "start" => $start_time,
                    "end" => $current_time,
                ];
                if (count($gantt_chart) > 1) {
                    if ($gantt_chart[count($gantt_chart) - 1]["process"] == $gantt_chart[count($gantt_chart) - 2]["process"]) {
                        $gantt_chart[count($gantt_chart) - 1]["start"] = $gantt_chart[count($gantt_chart) - 2]["start"];
                        unset($gantt_chart[count($gantt_chart) - 2]);
                        $gantt_chart = array_values($gantt_chart);
                    }
                }
            }

            while ($j < count($processes)) {
                $is_process = false;
                for ($i = 0; $i < count($processes); $i++) {
                    if ($processes[$i]['arrival_time'] <= $current_time && !$is_process_complete[$i]) {
                        $is_process = true;
                        if (!empty($request_queue[0]) && $request_queue[0] == $processes[$i]["process"]) {
                            unset($request_queue[0]);
                            process_request($i, $j, $processes, $quantom_time, $current_time, $remaining_burst_time, $is_process_complete, $is_process_start, $finish_time, $request_queue, $turnaround_time, $waitng_time, $gantt_chart, $response_time);
                            $request_queue = array_values($request_queue);
                        } else if (empty($request_queue[0])) {
                            process_request($i, $j, $processes, $quantom_time, $current_time, $remaining_burst_time, $is_process_complete, $is_process_start, $finish_time, $request_queue, $turnaround_time, $waitng_time, $gantt_chart, $response_time);
                        }
                    }
                }

                if (!$is_process) {
                    $gantt_chart[] = [
                        "process" => "-",
                        "start" => $current_time,
                        "end" => $current_time + 1,
                    ];
                    $current_time++;
                    continue;
                }
            }

            ksort($finish_time);
            ksort($turnaround_time);
            ksort($waitng_time);
            ksort($response_time);

            for ($i = 0; $i < count($processes); $i++) {
                array_push($processes[$i], [
                    "finish_time" => $finish_time[$i],
                    "turnaround_time" => $turnaround_time[$i],
                    "waiting_time" => $waitng_time[$i],
                    "response_time" => $response_time[$i],
                ]);
                $processes[$i] = array_merge($processes[$i], $processes[$i][0]);
                unset($processes[$i][0]);
            }

            $col_turnaround = collect($turnaround_time);
            $avg_turnaround = $col_turnaround->avg();

            $col_waiting = collect($waitng_time);
            $avg_waiting = $col_waiting->avg();

            $col_response = collect($response_time);
            $avg_response = $col_response->avg();

            usort($processes, function ($a, $b) {
                return $a['process'] <=> $b['process'];
            });

            return response()->json(
                [
                    "Process" => $processes,
                    "avg_turnaround" => substr($avg_turnaround, 0, 5),
                    "avg_waiting" => substr($avg_waiting, 0, 5),
                    "avg_response" => substr($avg_response, 0, 5),
                    "chart" => $gantt_chart
                ]
            );
        }
    }

    public function srtf(Request $request)
    {
        if ($request->input("Algorithm") == "SRTF") {
            $arrival_array = explode(' ', $request->get("Arrival"));
            $burst_array = explode(' ', $request->get('Burst'));

            for ($i = 0; $i < count($arrival_array); $i++) {
                $processes[$i] = [
                    "process" => "P" . $i + 1,
                    "arrival_time" => $arrival_array[$i],
                    "burst_time" => $burst_array[$i],
                ];
            }

            usort($processes, function ($a, $b) {
                return $a['arrival_time'] <=> $b['arrival_time'];
            });

            $j = 0;
            $current_time = 0;
            $arrival_time = 0;
            $finish_time = [];
            $turnaround_time = [];
            $waiting_time = [];
            $response_time = array_fill(0, count($processes), 0);
            $remaining_burst_time = array_column($processes, 'burst_time');
            $remaining_arrival_time = array_column($processes, 'arrival_time');
            $is_process_complete = array_fill(0, count($processes), false);
            $is_process_start = array_fill(0, count($processes), false);
            $gantt_chart = [];

            function finish_process(&$current_time, &$remaining_burst_time, &$index_process, &$is_process_complete, &$finish_time, &$j, &$turnaround_time, &$waiting_time, &$processes, &$gantt_chart)
            {
                $start = $current_time;
                $current_time += $remaining_burst_time[$index_process];
                $remaining_burst_time[$index_process] = 0;
                $is_process_complete[$index_process] = true;
                $finish_time[$index_process] = $current_time;
                $turnaround_time[$index_process] = $finish_time[$index_process] - $processes[$index_process]["arrival_time"];
                $waiting_time[$index_process] = $turnaround_time[$index_process] - $processes[$index_process]["burst_time"];
                $j++;
                $gantt_chart[] = [
                    "process" => $processes[$index_process]["process"],
                    "start" => $start,
                    "end" => $current_time,
                ];
                if (count($gantt_chart) > 1) {
                    if ($gantt_chart[count($gantt_chart) - 1]["process"] == $gantt_chart[count($gantt_chart) - 2]["process"]) {
                        $gantt_chart[count($gantt_chart) - 1]["start"] = $gantt_chart[count($gantt_chart) - 2]["start"];
                        unset($gantt_chart[count($gantt_chart) - 2]);
                        $gantt_chart = array_values($gantt_chart);
                    }
                }
            }

            while ($j < count($processes)) {
                $index_process = -1;
                $shortest_burst_process = PHP_INT_MAX;

                for ($i = 0; $i < count($processes); $i++) {
                    if ($processes[$i]["arrival_time"] <= $current_time && !$is_process_complete[$i] && $remaining_burst_time[$i] < $shortest_burst_process) {
                        $shortest_burst_process = $remaining_burst_time[$i];
                        $index_process = $i;
                    }
                }

                if ($index_process == -1) {
                    $gantt_chart[] = [
                        "process" => "-",
                        "start" => $current_time,
                        "end" => $current_time + 1,
                    ];
                    $current_time++;
                    continue;
                }

                if (!$is_process_start[$index_process]) {
                    $is_process_start[$index_process] = true;
                    $response_time[$index_process] = $current_time - $processes[$index_process]["arrival_time"];
                }

                if ($current_time >= $remaining_arrival_time[count($remaining_arrival_time) - 1]) {
                    finish_process($current_time, $remaining_burst_time, $index_process, $is_process_complete, $finish_time, $j, $turnaround_time, $waiting_time, $processes, $gantt_chart);
                } else {
                    foreach ($processes as $i => $process) {
                        if ($process["arrival_time"] > $current_time && !$is_process_complete[$i]) {
                            $arrival_time = $processes[$i]["arrival_time"];
                            break;
                        }
                    }
                    if ($arrival_time <= ($current_time + $remaining_burst_time[$index_process])) {
                        $gantt_chart[] = [
                            "process" => $processes[$index_process]["process"],
                            "start" => $current_time,
                            "end" => $arrival_time,
                        ];
                        $gantt_chart = $this->removeDuplicateGanttChart($gantt_chart);
                        $diffrent = $arrival_time - $current_time;
                        $remaining_burst_time[$index_process] -= $diffrent;
                        $current_time += $diffrent;
                    } else {
                        finish_process($current_time, $remaining_burst_time, $index_process, $is_process_complete, $finish_time, $j, $turnaround_time, $waiting_time, $processes, $gantt_chart);
                    }
                }
            }

            for ($i = 0; $i < count($processes); $i++) {
                array_push($processes[$i], [
                    "finish_time" => $finish_time[$i],
                    "turnaround_time" => $turnaround_time[$i],
                    "waiting_time" => $waiting_time[$i],
                    "response_time" => $response_time[$i],
                ]);
                $processes[$i] = array_merge($processes[$i], $processes[$i][0]);
                unset($processes[$i][0]);
            }

            $col_turnaround = collect($turnaround_time);
            $avg_turnaround = $col_turnaround->avg();

            $col_waiting = collect($waiting_time);
            $avg_waiting = $col_waiting->avg();

            $col_response = collect($response_time);
            $avg_response = $col_response->avg();

            usort($processes, function ($a, $b) {
                return $a['process'] <=> $b['process'];
            });

            return response()->json(
                [
                    "Process" => $processes,
                    "avg_turnaround" => substr($avg_turnaround, 0, 5),
                    "avg_waiting" => substr($avg_waiting, 0, 5),
                    "avg_response" => substr($avg_response, 0, 5),
                    "chart" => $gantt_chart
                ]
            );
        }
    }

    public function lrtf(Request $request)
    {
        if ($request->input("Algorithm") == "LRTF") {
            $arrival_array = explode(' ', $request->get("Arrival"));
            $burst_array = explode(' ', $request->get('Burst'));

            for ($i = 0; $i < count($arrival_array); $i++) {
                $processes[$i] = [
                    "process" => "P" . $i + 1,
                    "arrival_time" => $arrival_array[$i],
                    "burst_time" => $burst_array[$i],
                ];
            }

            usort($processes, function ($a, $b) {
                return $a['arrival_time'] <=> $b['arrival_time'];
            });

            $j = 0;
            $current_time = 0;
            $arrival_time = 0;
            $finish_time = [];
            $turnaround_time = [];
            $waiting_time = [];
            $response_time = [];
            $remaining_burst_time = array_column($processes, 'burst_time');
            $is_process_complete = array_fill(0, count($processes), false);
            $is_process_start = array_fill(0, count($processes), false);
            $gantt_chart = [];

            while ($j < count($processes)) {
                $index_process = -1;
                $longest_burst_process = 0;

                for ($i = 0; $i < count($processes); $i++) {
                    if ($processes[$i]["arrival_time"] <= $current_time && !$is_process_complete[$i] && $remaining_burst_time[$i] > $longest_burst_process) {
                        $longest_burst_process = $remaining_burst_time[$i];
                        $index_process = $i;
                    }
                }

                if ($index_process == -1) {
                    $gantt_chart[] = [
                        "process" => "-",
                        "start" => $current_time,
                        "end" => $current_time + 1,
                    ];
                    $current_time++;
                    continue;
                }

                if (!$is_process_start[$index_process]) {
                    $response_time[$index_process] = $current_time - $processes[$index_process]["arrival_time"];
                    $is_process_start[$index_process] = true;
                }
                $gantt_chart[] = [
                    "process" => $processes[$index_process]["process"],
                    "start" => $current_time,
                    "end" => $current_time + 1,
                ];
                $gantt_chart = $this->removeDuplicateGanttChart($gantt_chart);
                $current_time++;
                $remaining_burst_time[$index_process] -= 1;
                if ($remaining_burst_time[$index_process] == 0) {
                    $is_process_complete[$index_process] = true;
                    $j++;
                    $finish_time[$index_process] = $current_time;
                    $turnaround_time[$index_process] = $finish_time[$index_process] - $processes[$index_process]["arrival_time"];
                    $waiting_time[$index_process] = $turnaround_time[$index_process] - $processes[$index_process]["burst_time"];
                }
            }
        }

        for ($i = 0; $i < count($processes); $i++) {
            array_push($processes[$i], [
                "finish_time" => $finish_time[$i],
                "turnaround_time" => $turnaround_time[$i],
                "waiting_time" => $waiting_time[$i],
                "response_time" => $response_time[$i],
            ]);
            $processes[$i] = array_merge($processes[$i], $processes[$i][0]);
            unset($processes[$i][0]);
        }

        $col_turnaround = collect($turnaround_time);
        $avg_turnaround = $col_turnaround->avg();

        $col_waiting = collect($waiting_time);
        $avg_waiting = $col_waiting->avg();

        $col_response = collect($response_time);
        $avg_response = $col_response->avg();

        usort($processes, function ($a, $b) {
            return $a['process'] <=> $b['process'];
        });

        return response()->json(
            [
                "Process" => $processes,
                "avg_turnaround" => substr($avg_turnaround, 0, 5),
                "avg_waiting" => substr($avg_waiting, 0, 5),
                "avg_response" => substr($avg_response, 0, 5),
                "chart" => $gantt_chart
            ]
        );
    }

    public function hrrn(Request $request)
    {
        if ($request->input("Algorithm") == "HRRN") {
            $arrival_array = explode(' ', $request->get("Arrival"));
            $burst_array = explode(' ', $request->get('Burst'));

            for ($i = 0; $i < count($arrival_array); $i++) {
                $processes[$i] = [
                    "process" => "P" . $i + 1,
                    "arrival_time" => $arrival_array[$i],
                    "burst_time" => $burst_array[$i],
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
            $response_time = [];
            $remaining_burst_time = array_column($processes, 'burst_time');
            $is_process_complete = array_fill(0, count($processes), false);
            $gantt_chart = [];

            while ($j < count($processes)) {
                $index_process = -1;
                $hight_response_ratio = -1;

                for ($i = 0; $i < count($processes); $i++) {
                    if ($processes[$i]['arrival_time'] <= $current_time && !$is_process_complete[$i]) {
                        $response_ration = (($current_time - $processes[$i]['arrival_time']) + $processes[$i]['burst_time']) / $processes[$i]['burst_time'];
                        if ($response_ration >= $hight_response_ratio) {
                            $hight_response_ratio = $response_ration;
                            $index_process = $i;
                        }
                    }
                }

                if ($index_process == -1) {
                    $gantt_chart[] = [
                        "process" => "-",
                        "start" => $current_time,
                        "end" => $current_time + 1,
                    ];
                    $current_time++;
                    continue;
                }

                $start = $current_time;
                $response_time[$index_process] = $start - $processes[$index_process]['arrival_time'];
                $current_time += $remaining_burst_time[$index_process];
                $remaining_burst_time[$index_process] = 0;
                $is_process_complete[$index_process] = true;
                $finish_time[$index_process] = $current_time;
                $turnaround_time[$index_process] = $finish_time[$index_process] - $processes[$index_process]['arrival_time'];
                $waiting_time[$index_process] = $turnaround_time[$index_process] - $processes[$index_process]['burst_time'];
                $j++;
                $gantt_chart[] = [
                    "process" => $processes[$index_process]['process'],
                    "start" => $start,
                    "end" => $current_time,
                ];
            }

            for ($i = 0; $i < count($processes); $i++) {
                array_push($processes[$i], [
                    "finish_time" => $finish_time[$i],
                    "turnaround_time" => $turnaround_time[$i],
                    "waiting_time" => $waiting_time[$i],
                    "response_time" => $response_time[$i],
                ]);
                $processes[$i] = array_merge($processes[$i], $processes[$i][0]);
                unset($processes[$i][0]);
            }

            $col_turnaround = collect($turnaround_time);
            $avg_turnaround = $col_turnaround->avg();

            $col_waiting = collect($waiting_time);
            $avg_waiting = $col_waiting->avg();

            $col_response = collect($response_time);
            $avg_response = $col_response->avg();

            usort($processes, function ($a, $b) {
                return $a['process'] <=> $b['process'];
            });

            return response()->json(
                [
                    "Process" => $processes,
                    "avg_turnaround" => substr($avg_turnaround, 0, 5),
                    "avg_waiting" => substr($avg_waiting, 0, 5),
                    "avg_response" => substr($avg_response, 0, 5),
                    "chart" => $gantt_chart
                ]
            );
        }
    }

    public function priority_none_preemptive(Request $request)
    {
        if ($request->input("Algorithm") == "NONPREEMPTIVE") {
            $arrival_array = explode(' ', $request->get("Arrival"));
            $burst_array = explode(' ', $request->get('Burst'));
            $priority_array = explode(' ', $request->get('Priority'));

            for ($i = 0; $i < count($arrival_array); $i++) {
                $processes[$i] = [
                    "process" => "P" . $i + 1,
                    "arrival_time" => $arrival_array[$i],
                    "burst_time" => $burst_array[$i],
                    "priority" => $priority_array[$i],
                ];
            }

            usort($processes, function ($a, $b) {
                return $a['arrival_time'] <=> $b['arrival_time'];
            });

            $j = 0;
            $current_time = 0;
            $finish_time = [];
            $turaround_time = [];
            $waiting_time = [];
            $response_time = [];
            $gantt_chart = [];
            $is_process_complete = array_fill(0, count($processes), false);
            $remaining_burst_time = array_column($processes, 'burst_time');
            $remaining_priority = array_column($processes, 'priority');

            while ($j < count($processes)) {
                $index_process = -1;
                $priority = PHP_INT_MAX;

                if ($current_time >= $processes[count($processes) - 1]["arrival_time"]) {
                    for ($i = 0; $i < count($processes); $i++) {
                        if (!$is_process_complete[$i] && $remaining_priority[$i] < $priority) {
                            $priority = $remaining_priority[$i];
                            $index_process = $i;
                        }
                    }
                } else {
                    for ($i = 0; $i < count($processes); $i++) {
                        if ($processes[$i]["arrival_time"] <= $current_time && !$is_process_complete[$i] && $remaining_priority[$i] < $priority) {
                            $index_process = $i;
                            $priority = $remaining_priority[$i];
                        }
                    }
                }

                if ($index_process == -1) {
                    $current_time++;
                    $gantt_chart[] = [
                        "process" => "-",
                        "start" => $current_time,
                        "end" => $current_time + 1,
                    ];
                    continue;
                }

                $start = $current_time;
                $response_time[$index_process] = $start - $processes[$index_process]["arrival_time"];
                $current_time += $remaining_burst_time[$index_process];
                $remaining_burst_time[$index_process] = 0;
                $is_process_complete[$index_process] = true;
                $finish_time[$index_process] = $current_time;
                $turaround_time[$index_process] = $finish_time[$index_process] - $processes[$index_process]["arrival_time"];
                $waiting_time[$index_process] = $turaround_time[$index_process] - $processes[$index_process]["burst_time"];
                $gantt_chart[] = [
                    "process" => $processes[$index_process]["process"],
                    "start" => $start,
                    "end" => $current_time,
                ];
                $j++;
            }

            for ($i = 0; $i < count($processes); $i++) {
                array_push($processes[$i], [
                    "finish_time" => $finish_time[$i],
                    "turnaround_time" => $turaround_time[$i],
                    "waiting_time" => $waiting_time[$i],
                    "response_time" => $response_time[$i],
                ]);
                $processes[$i] = array_merge($processes[$i], $processes[$i][0]);
                unset($processes[$i][0]);
            }

            $col_turnaround = collect($turaround_time);
            $avg_turnaround = $col_turnaround->avg();

            $col_waiting = collect($waiting_time);
            $avg_waiting = $col_waiting->avg();

            $col_response = collect($response_time);
            $avg_response = $col_response->avg();

            usort($processes, function ($a, $b) {
                return $a['process'] <=> $b['process'];
            });

            return response()->json(
                [
                    "Process" => $processes,
                    "avg_turnaround" => substr($avg_turnaround, 0, 5),
                    "avg_waiting" => substr($avg_waiting, 0, 5),
                    "avg_response" => substr($avg_response, 0, 5),
                    "chart" => $gantt_chart
                ]
            );
        }
    }

    public function priority_preemptive(Request $request)
    {
        if ($request->input("Algorithm") == "PREEMPTIVE") {
            $arrival_array = explode(' ', $request->get("Arrival"));
            $burst_array = explode(' ', $request->get('Burst'));
            $priority_array = explode(' ', $request->get('Priority'));

            for ($i = 0; $i < count($arrival_array); $i++) {
                $processes[$i] = [
                    "process" => "P" . $i + 1,
                    "arrival_time" => $arrival_array[$i],
                    "burst_time" => $burst_array[$i],
                    "priority" => $priority_array[$i],
                ];
            }

            usort($processes, function ($a, $b) {
                return $a['arrival_time'] <=> $b['arrival_time'];
            });

            $j = 0;
            $current_time = 0;
            $arrival_time = 0;
            $finish_time = [];
            $turnaround_time = [];
            $waiting_time = [];
            $response_time = [];
            $remaining_burst_time = array_column($processes, 'burst_time');
            $remaining_priority_time = array_column($processes, 'priority');
            $is_process_complete = array_fill(0, count($processes), false);
            $is_process_start = array_fill(0, count($processes), false);
            $gantt_chart = [];

            function finish_process(&$current_time, &$remaining_burst_time, &$index_process, &$is_process_complete, &$finish_time, &$j, &$turnaround_time, &$waiting_time, &$processes, &$gantt_chart)
            {
                $start = $current_time;
                $current_time += $remaining_burst_time[$index_process];
                $remaining_burst_time[$index_process] = 0;
                $is_process_complete[$index_process] = true;
                $finish_time[$index_process] = $current_time;
                $turnaround_time[$index_process] = $finish_time[$index_process] - $processes[$index_process]["arrival_time"];
                $waiting_time[$index_process] = $turnaround_time[$index_process] - $processes[$index_process]["burst_time"];
                $j++;
                $gantt_chart[] = [
                    "process" => $processes[$index_process]["process"],
                    "start" => $start,
                    "end" => $current_time,
                ];
                if (count($gantt_chart) > 1) {
                    if ($gantt_chart[count($gantt_chart) - 1]["process"] == $gantt_chart[count($gantt_chart) - 2]["process"]) {
                        $gantt_chart[count($gantt_chart) - 1]["start"] = $gantt_chart[count($gantt_chart) - 2]["start"];
                        unset($gantt_chart[count($gantt_chart) - 2]);
                        $gantt_chart = array_values($gantt_chart);
                    }
                }
            }

            while ($j < count($processes)) {
                $index_process = -1;
                $shortest_priority_process = PHP_INT_MAX;

                for ($i = 0; $i < count($processes); $i++) {
                    if ($processes[$i]["arrival_time"] <= $current_time && !$is_process_complete[$i] && $remaining_priority_time[$i] < $shortest_priority_process) {
                        $shortest_priority_process = $remaining_priority_time[$i];
                        $index_process = $i;
                    }
                }

                if ($index_process == -1) {
                    $gantt_chart[] = [
                        "process" => "-",
                        "start" => $current_time,
                        "end" => $current_time + 1,
                    ];
                    $current_time++;
                    continue;
                }

                if (!$is_process_start[$index_process]) {
                    $response_time[$index_process] = $current_time - $processes[$index_process]["arrival_time"];
                    $is_process_start[$index_process] = true;
                }


                if ($current_time >= $processes[count($processes) - 1]["arrival_time"]) {
                    finish_process($current_time, $remaining_burst_time, $index_process, $is_process_complete, $finish_time, $j, $turnaround_time, $waiting_time, $processes, $gantt_chart);
                } else {
                    foreach ($processes as $i => $process) {
                        if ($process["arrival_time"] > $current_time && !$is_process_complete[$i]) {
                            $arrival_time = $processes[$i]["arrival_time"];
                            break;
                        }
                    }
                    if ($arrival_time <= ($current_time + $remaining_burst_time[$index_process])) {
                        $gantt_chart[] = [
                            "process" => $processes[$index_process]["process"],
                            "start" => $current_time,
                            "end" => $arrival_time,
                        ];
                        $gantt_chart = $this->removeDuplicateGanttChart($gantt_chart);
                        $diffrent = $arrival_time - $current_time;
                        $remaining_burst_time[$index_process] -= $diffrent;
                        $current_time += $diffrent;
                    } else {
                        finish_process($current_time, $remaining_burst_time, $index_process, $is_process_complete, $finish_time, $j, $turnaround_time, $waiting_time, $processes, $gantt_chart);
                    }
                }
            }

            for ($i = 0; $i < count($processes); $i++) {
                array_push($processes[$i], [
                    "finish_time" => $finish_time[$i],
                    "turnaround_time" => $turnaround_time[$i],
                    "waiting_time" => $waiting_time[$i],
                    "response_time" => $response_time[$i],
                ]);
                $processes[$i] = array_merge($processes[$i], $processes[$i][0]);
                unset($processes[$i][0]);
            }

            $col_turnaround = collect($turnaround_time);
            $avg_turnaround = $col_turnaround->avg();

            $col_waiting = collect($waiting_time);
            $avg_waiting = $col_waiting->avg();

            $col_response = collect($response_time);
            $avg_response = $col_response->avg();

            usort($processes, function ($a, $b) {
                return $a['process'] <=> $b['process'];
            });

            return response()->json(
                [
                    "Process" => $processes,
                    "avg_turnaround" => substr($avg_turnaround, 0, 5),
                    "avg_waiting" => substr($avg_waiting, 0, 5),
                    "avg_response" => substr($avg_response, 0, 5),
                    "chart" => $gantt_chart
                ]
            );
        }
    }

    private function removeDuplicateGanttChart($gantt_chart)
    {
        if (count($gantt_chart) > 1) {
            if ($gantt_chart[count($gantt_chart) - 1]["process"] == $gantt_chart[count($gantt_chart) - 2]["process"]) {
                $gantt_chart[count($gantt_chart) - 1]["start"] = $gantt_chart[count($gantt_chart) - 2]["start"];
                unset($gantt_chart[count($gantt_chart) - 2]);
                $gantt_chart = array_values($gantt_chart);
            }
        }
        return $gantt_chart;
    }
}
