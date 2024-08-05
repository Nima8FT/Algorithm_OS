<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;

class BankerController extends Controller
{
    public function banker(Request $request)
    {
        if ($request->input("Algorithm") == "banker") {
            $allocation = explode('#', $request->get('Allocation'));
            $allocation_matrices = [];
            foreach ($allocation as $key => $value) {
                $allocation_matrices["P" . ($key + 1)] = array_map('intval', explode(',', $value));
            }


            $max = explode('#', $request->get('Max'));
            $max_matrices = [];
            foreach ($max as $key => $value) {
                $max_matrices["P" . ($key + 1)] = array_map('intval', explode(',', $value));
            }

            $instance = array_map('intval', explode(' ', $request->get('Instance')));

            $allocation_array = array_fill(0, count($instance), 0);
            foreach ($allocation_matrices as $allocation_matrix) {
                for ($j = 0; $j < count($instance); $j++) {
                    $allocation_array[$j] += $allocation_matrix[$j];
                }
            }

            $available = [];
            for ($i = 0; $i < count($instance); $i++) {
                $available[$i] = $instance[$i] - $allocation_array[$i];
            }



            $need = [];
            for ($i = 0; $i < count($max); $i++) {
                for ($j = 0; $j < count($instance); $j++) {
                    $need["P" . $i + 1][$j] = $max_matrices["P" . $i + 1][$j] - $allocation_matrices["P" . $i + 1][$j];
                }
            }

            $available_array["Resource"] = $available;
            $is_process_complete = array_fill(0, count($need), false);
            $index = -1;
            $k = 0;
            $iteration_limit = 100;
            $iteration_count = 0;

            while ($k < count($need)) {
                for ($j = 0; $j < count($instance); $j++) {
                    if ($need["P" . $k + 1][$j] > $available[$j]) {
                        $index = -1;
                        break;
                    }
                    $index = $k;
                }

                if ($index !== -1 && !$is_process_complete[$index]) {
                    for ($j = 0; $j < count($instance); $j++) {
                        $available[$j] += $allocation_matrices["P" . $index + 1][$j];
                        $available_array["P" . $index + 1] = $available;
                    }
                    $is_process_complete[$index] = true;
                }


                if ($k == count($need) - 1) {
                    for ($i = 0; $i < count($need); $i++) {
                        if (!$is_process_complete[$i]) {
                            $k = -1;
                            break;
                        }
                    }
                }

                $k++;
                $iteration_count++;
                if ($iteration_count > $iteration_limit) {
                    break;
                }
            }

            $all_processes_completed = array_reduce($is_process_complete, function ($carry, $item) {
                return $carry && $item;
            }, true);

            if (!$all_processes_completed) {
                return response()->json(['status' => false, 'message' => 'Deadlock detected or system is not in a safe state']);
            } else {
                $process_sequence = [];
                foreach ($available_array as $key => $value) {
                    array_push($process_sequence, $key);
                }
                unset($process_sequence[0]);
                $process_sequence = array_values($process_sequence);

                return response()->json(
                    [
                        'status' => true,
                        "Instance" => $instance,
                        "Allocation" => $allocation_matrices,
                        "Max" => $max_matrices,
                        "Need" => $need,
                        "Available" => $available_array,
                        "Process Sequence" => $process_sequence,
                    ],
                );
            }
        }
    }
}
