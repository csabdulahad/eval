<?php
	
	namespace A2\Eval\api\eval;
	
	use hati\api\HatiAPI;
	use hati\api\Response;
	use hati\cli\CLI;
	use hati\Hati;
	use hati\Trunk;
	use hati\util\Benchmark;
	use hati\util\Shomoy;
	use JetBrains\PhpStorm\NoReturn;
	
	HatiAPI::noDirectAccess();
	
	class EvalPHP extends HatiAPI {
		
		private int $exeLimit = 30;
		private string $scriptDir;
		
		public function __construct() {
			$this -> scriptDir = Hati::root('script');
		}
		
		private function getFilepath(string $filename): string {
			return $this -> scriptDir . DIRECTORY_SEPARATOR . $filename;
		}
		
		private function getFileName (): string {
			$file = $this -> param('file', null);
			if (is_null($file)) throw Trunk::error400('File name is missing');
			
			return $file;
		}
		
		private function last_file_index (): int {
			return count(scandir(Hati::root('script'))) - 2;
		}
		
		#[NoReturn]
		public function execute(): void {
			$fileName = $this -> getFileName();
			$file = $this -> getFilepath($fileName);
			$codeFile = Hati::root('runner/code.php');
			
			$template = file_get_contents(Hati::root('runner/template.php'));
			
			$code = substr(file_get_contents($file), 5);
			$code = str_replace('// eval_code_X', $code, $template);
			
			$handler = fopen($codeFile, 'w+');
			
			if (!$handler) {
				throw Trunk::error500('Failed to prepare execution environment');
			}
			
			fputs($handler, $code);
			fclose($handler);
			
			$cmd = "php $codeFile";
			
			$descriptor = [
				0 => ["pipe", "r"],
				1 => ["pipe", "w"],
				2 => ["pipe", "w"],
			];
			$pipes = [];
			
			Benchmark::start('exe');
			$shomoy = new Shomoy();
			
			$output = '';
			$benchmarkInfo = "<span class='output-benchmark'>[" . $shomoy -> format('d/m/y H:i:s') . "] [$fileName]";
			
			$process = proc_open($cmd, $descriptor, $pipes, cwd: $this->scriptDir);
			$status = proc_get_status($process);
			$pkId = -1;
			
			if (is_resource($process)) {
				$startTime = microtime(true);
				
				// Start an infinite loop
				while ($status['running'] === true) {
					// Update the status within the loop
					$status = proc_get_status($process);
					
					if ((microtime(true) - $startTime) >= $this -> exeLimit) {
					
						$pkId = $status['pid'];
						break;
					}
					
					// Sleep for 50ms to prevent eating up CPU in this loop
					usleep(50000);
				}
				
				Benchmark::end('exe');
				
				$execution = Benchmark::getMark('exe');
				$benchmarkInfo .= " [$execution sec, ";
				
				if ($pkId == -1) {
					while ($line = fgets($pipes[1])) {
						if (str_starts_with($line, "eval_mem_X")) {
							$line = substr($line, strlen("eval_mem_x"));
							
							$benchmarkInfo .= $line . "]</span>\n";
							$output = $benchmarkInfo . $output;
							
							continue;
						}
						$output .= $line;
					}
				} else {
					if (CLI::isWindows()) {
						$command =  'powershell.exe "$ok = 1; Get-WmiObject Win32_Process | Where-Object {$_.ParentProcessID -eq ' . $pkId . '} | ForEach-Object { try { Stop-Process -ID $_.ProcessID -Force -ErrorAction Stop } catch { $ok = -1; } }; Write-Host $ok"';
						$killOutput = shell_exec($command);
						
						$killOutput = $killOutput != 1 ? 'Kill the PHP CLI in taskbar manually' : "Script took more than $this->exeLimit seconds and terminated forcefully!";
					} else {
						$killOutput = 'Please kill handing thread on Linux/Mac manually';
					}
					
					$killOutput = "<span class='output-script-terminated'>$killOutput</span>";
					
					$output .= $killOutput;
				}
				
				fclose($pipes[0]);
				fclose($pipes[1]);
				fclose($pipes[2]);
				proc_close($process);
			}
			
			if (!str_starts_with($output, "<span class='output-benchmark'>")) {
				$output = $benchmarkInfo . " 0 kb]</span>\n" . $output;
			}
			
			$res = new Response();
			$res -> add('output', $output);
			$res -> reply();
		}
		
		#[NoReturn]
		public function delete(): void {
			$file = $this -> getFileName();
			
			chdir($this -> scriptDir);
			
			if (!file_exists($file)) {
				throw Trunk::error400("File $file doesn't exist");
			}
			
			if (!unlink($file)) {
				throw Trunk::error500("Failed to delete $file");
			}
			
			Response::reportOk("File $file deleted");
		}
		
		#[NoReturn]
		public function post (): void {
			$file = $this -> getFileName();
			$filePath = $this -> getFilepath($file);
			$code = substr($this -> requestBody('raw'), 2);
			
			if (file_put_contents($filePath, $code) === false) {
				throw Trunk::error500('Failed to save the code');
			}
			
			Response::reportOk("$file saved");
		}
		
		#[NoReturn]
		public function get (): void {
			$dir = $this -> scriptDir;
			$files = array_diff(scandir($dir), array('.', '..'));
			
			$files = array_map(function ($file) use ($dir) {
				
				return [
					'filename' => $dir . '/' . $file,
					'ctime' => filectime($dir . '/' . $file)
				];
			}, $files);
			
			usort($files, function ($file1, $file2) {
				return $file1['ctime'] <=> $file2['ctime'];
			});
			
			$files = array_map(function ($f) {
				return basename($f['filename']);
			}, $files);
			
			$res = new Response();
			$res -> add('files', $files);
			$res -> reply();
		}
		
		#[NoReturn]
		public function fetch (): void {
			$file = $this -> getFileName();
			
			$filPath = $this -> scriptDir . DIRECTORY_SEPARATOR . $file;
			if (!file_exists($filPath)) {
				throw Trunk::error400("File $file doesn't exits");
			}
			
			$fileContent = file_get_contents($filPath);
			
			$res = new Response();
			$res -> add('data', $fileContent);
			$res -> reply();
		}
		
		#[NoReturn]
		public function rename(): void {
			$from 	= $this -> param('from', null);
			$to 	= $this -> param('to', null);
			
			if (empty($from) || empty($to)) {
				throw Trunk::error401('Both old and new file name are required');
			}
			
			chdir($this -> scriptDir);
			
			if (!file_exists($from)) {
				throw Trunk::error401("File $from doesn't exist");
			}
			
			if (file_exists($to)) {
				throw Trunk::error401("File $to already exists");
			}
			
			if (!rename($from, $to)) {
				throw Trunk::error500("Failed to rename file $from to $to");
			}
			
			Response::reportOk("Renamed to $to");
		}
		
		#[NoReturn]
		public function create (): void {
			$fileName = 'PHP ' . ($this -> last_file_index() + 1) . '.php';
			$file = $this -> getFilepath($fileName);
			
			if (file_exists($file)) {
				$fileName = 'PHP ' . ($this -> last_file_index() + 2) . '.php';
				$file = $this -> getFilepath($fileName);
			}
			
			$handler = fopen($file, 'w');
			if (!$handler) {
				throw Trunk::error400("Failed to create file $fileName");
			}
			
			fputs($handler, "<?php\n\n\t");
			fclose($handler);
			
			$res = new Response();
			$res -> add('filename', $fileName);
			$res -> reply("File $fileName has been created");
		}
		
	}