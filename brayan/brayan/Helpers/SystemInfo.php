<?php

class SystemInfo
{
  private $plataform = "BASH";

  public function __construct()
  {
    if (PHP_OS_FAMILY == "Windows") {
      $this->plataform = "MS";
    }
  }

  public function getIsWindows()
  {
    return $this->plataform == "MS";
  }

  public function getCpuCount(): int
  {
    if ($this->getIsWindows()) {
      return (int) getenv("NUMBER_OF_PROCESSORS") ?: 1;
    } else {
      return (int) shell_exec("nproc") ?: 1;
    }
  }

  public function getSystemLoad(): array
  {
    if ($this->getIsWindows()) {
      $load = shell_exec("wmic cpu get LoadPercentage /value");
      if ($load) {
        preg_match('/LoadPercentage=(\d+)/', $load, $matches);
        return isset($matches[1]) ? [round((int) $matches[1] / 100, 2)] : [0.00];
      }
      return [0.00];
    } else {
      return array_map(fn($val) => round($val, 2), sys_getloadavg());
    }
  }

  public function getCpuInfo(): array
  {
    $cpuTotal = (int) trim(shell_exec(PHP_OS_FAMILY === 'Windows' ? "wmic CPU Get NumberOfLogicalProcessors /value | findstr NumberOfLogicalProcessors" : "nproc"));

    if ($this->getIsWindows()) {
      $cpuUsed = (int) trim(shell_exec("wmic cpu get LoadPercentage /value | findstr LoadPercentage"));
      $cpuFree = 100 - $cpuUsed;
      return [
        'total' => $cpuTotal,
        'free' => round($cpuFree, 2),
        'used' => round($cpuUsed, 2),
      ];
    } else {
      $cpuIdle = trim(shell_exec("mpstat 1 1 | awk '/Average:/ {print $12}'"));
      $cpuFree = is_numeric($cpuIdle) ? (float) $cpuIdle : 0;
      $cpuUsed = 100 - $cpuFree;
      return [
        'total' => $cpuTotal,
        'free' => round($cpuFree, 2),
        'used' => round($cpuUsed, 2),
      ];
    }
  }

  public function getMemoryInfo(): array
  {
    if ($this->getIsWindows()) {
      $memTotal = (int) trim(shell_exec("wmic OS get TotalVisibleMemorySize /value | findstr TotalVisibleMemorySize"));
      $memFree = (int) trim(shell_exec("wmic OS get FreePhysicalMemory /value | findstr FreePhysicalMemory"));

      if ($memTotal > 0) {
        $memTotalMB = round($memTotal / 1024, 2); // Convertir KB a MB
        $memFreeMB = round($memFree / 1024, 2);
        $memUsedMB = round($memTotalMB - $memFreeMB, 2);
        $memUsagePercent = round(($memUsedMB / $memTotalMB) * 100, 2);

        return [
          'total' => $memTotalMB,
          'free' => $memFreeMB,
          'used' => $memUsedMB,
          'usage_percent' => $memUsagePercent
        ];
      } else {
        return [
          'total' => 0.0,
          'free' => 0.0,
          'used' => 0.0,
          'usage_percent' => 0.0
        ];
      }
    } else {
      $memInfo = shell_exec("free -m");
      if ($memInfo) {
        $lines = explode("\n", trim($memInfo));
        $data = preg_split('/\s+/', $lines[1]); // Segunda línea de `free -m`

        $memTotalMB = (float) $data[1];
        $memUsedMB = (float) $data[2];
        $memFreeMB = (float) $data[3];
        $memUsagePercent = round(($memUsedMB / $memTotalMB) * 100, 2);

        return [
          'total' => $memTotalMB,
          'free' => $memFreeMB,
          'used' => $memUsedMB,
          'usage_percent' => $memUsagePercent
        ];
      } else {
        return [
          'total' => 0.0,
          'free' => 0.0,
          'used' => 0.0,
          'usage_percent' => 0.0
        ];
      }
    }
  }
}