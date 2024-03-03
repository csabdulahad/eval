<?php register_shutdown_function('eval_benchmark_X');require realpath(__DIR__ . '/../hati/init.php');$eval_mem_usage_X =  memory_get_usage(); // eval_code_X

/*
 * Calculate the memory usage
 * */
$eval_mem_usage_X = memory_get_usage() - $eval_mem_usage_X;
function eval_benchmark_X(): void {
	global $eval_mem_usage_X;
	
	if ($eval_mem_usage_X == 0) {
		echo "\neval_mem_X 0 kb";
		return;
	}
	
	$unit = ['b','kb','mb','gb','tb','pb'];
	echo "\neval_mem_X" . @round( $eval_mem_usage_X / pow(1024, ($i = floor(log($eval_mem_usage_X,1024)))),2) .' '.$unit[$i];
}
