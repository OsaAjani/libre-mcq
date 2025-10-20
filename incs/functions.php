<?php

    /**
     * Get a list of MCQs from the data directory.
     * 
     * @param string $data_dir The path to the data directory.
     * @param string|null $status Optional status filter (e.g., 'open').
     */
    function get_mcqs($data_dir, $status = null) 
    {
        $open_mcqs = [];
        if (is_dir($data_dir)) 
        {
            $dirs = scandir($data_dir);
            foreach ($dirs as $dir) 
            {
                if ($dir === '.' || $dir === '..') 
                {
                    continue;
                }

                $dir_path = $data_dir . DIRECTORY_SEPARATOR . $dir;
                if (!is_dir($dir_path)) 
                {
                    continue;
                }
                
                if ($status === null) 
                {
                    $open_mcqs[] = read_mcq_data($dir_path);
                    continue;
                }

                $status_file = $dir_path . DIRECTORY_SEPARATOR . 'status.txt';
                if (file_exists($status_file) && trim(file_get_contents($status_file)) === $status) 
                {
                    $open_mcqs[] = read_mcq_data($dir_path);
                }
            }
        }

        return $open_mcqs;
    }

    function read_mcq_data($mcq_dir) 
    {
        $result = null;
        $data_file = $mcq_dir . DIRECTORY_SEPARATOR . 'mcq.json';
        if (file_exists($data_file)) 
        {
            $json_content = file_get_contents($data_file);
            $result = json_decode($json_content, true);
        }

        if (file_exists($mcq_dir . DIRECTORY_SEPARATOR . 'status.txt')) 
        {
            $result['status'] = trim(file_get_contents($mcq_dir . DIRECTORY_SEPARATOR . 'status.txt'));
        }

        $result['id'] = basename($mcq_dir);

        return $result;
    }

    function flash($type, $message)
    {
        $flashes = $_SESSION['flashes'] ?? [];
        $flashes[] = ['type' => $type, 'message' => $message];

        $_SESSION['flashes'] = $flashes;
    }

    function get_flashes()
    {
        $flashes = $_SESSION['flashes'];
        $_SESSION['flashes'] = [];
        return $flashes;
    }