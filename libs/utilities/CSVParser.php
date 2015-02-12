<?php
/**
 * Convert a string or a file in a CSV format into a two dimensions array
 * 
 * Based in the Ming Hong Ng version
 * http://minghong.blogspot.com/2006/07/csv-parser-for-php.html
 *
 * @author Ming Hong Ng
 * @author elalecs
 * @version 1.0
 *
 */
class utilities_CSVParser
{
    private $data = null; // CSV data as 2D array
    private $settings = null;

    function __construct()
    {
    	$this->settings = (object) array(
    		'delimiter' => ',', // Field delimiter
    		'enclosure' => '"', // Field enclosure character
    		'input_encoding' => '', // Input character encoding, empty for auto
    		'output_encoding' => 'UTF-8', // Output character encoding
    		'headers' => true
    	);
    	
        $this->data = array();
    }
    
    /**
     * Modify the settings using an array with the desired changes
     * @param array $settings
     */
    function settings($settings) {
    	$this->settings = (object) ( ((array) $settings) + ((array) $this->settings) );
    }

    /**
     * Parse CSV from file
     * @param   content     The CSV filename
     * @param   hasBOM      Using BOM or not
     * @return Success or not
     */
    function fromFile( $filename, $hasBOM = false )
    {
        if ( !is_readable($filename) )
        {
            return false;
        }
        return $this->fromString(file_get_contents($filename), $hasBOM);
    }

    /**
     * Parse CSV from string
     * @param   content     The CSV string
     * @param   hasBOM      Using BOM or not
     * @return Success or not
     */
    function fromString($content, $hasBOM = false)
    {
    	if (empty($this->settings->input_encoding)) {
    		$this->settings->input_encoding = mb_detect_encoding($content, 'ASCII, UTF-8, ISO-8859-2, ISO-8859-1');
    		if ($this->settings->input_encoding === "ISO-8859-2") {
    			$this->settings->input_encoding = "MACROMAN";
    		}
    	}
        $content = iconv($this->settings->input_encoding, $this->settings->output_encoding, $content);
        $content = str_replace("\r\n", "\n", $content);
        $content = str_replace("\r", "\n", $content);
        if ($hasBOM) // Remove the BOM (first 3 bytes)
        {
            $content = substr( $content, 3 );
        }
        if ($content[strlen($content) - 1] != "\n" ) // Make sure it always end with a newline
        {
            $content .= "\n";
        }

        // Parse the content character by character
        $row = array( "" );
        $idx = 0;
        $quoted = false;
        for ( $i = 0; $i < strlen($content); $i++ )
        {
            $ch = $content[$i];
            if ( $ch == $this->settings->enclosure )
            {
                $quoted = !$quoted;
            }

            // End of line
            if ( $ch == "\n" && !$quoted )
            {
                // Remove enclosure delimiters
                for ( $k = 0; $k < count($row); $k++ )
                {
                    if ( $row[$k] != "" && $row[$k][0] == $this->settings->enclosure )
                    {
                        $row[$k] = substr( $row[$k], 1, strlen($row[$k]) - 2 );
                    }
                    $row[$k] = str_replace( str_repeat($this->settings->enclosure, 2), $this->settings->enclosure, $row[$k] );
                }

                // Append row into table
                $this->data[] = $row;
                $row = array( "" );
                $idx = 0;
            }

            // End of field
            else if ( $ch == $this->settings->delimiter && !$quoted )
            {
                $row[++$idx] = "";
            }

            // Inside the field
            else
            {
                $row[$idx] .= $ch;
            }
        }

        return true;
    }
    
    /**
     * Return the data like hashtable using the first row as headers
     * @return array
     */
    public function getData() {
    	if (!$this->settings->headers) {
    		return $this->data;
    	}
    	
    	$data = array();
    	
    	$headers = $this->data[0];
    	unset($this->data[0]);
    	
    	foreach($this->data as $row) {
    		$_row = array();
    		foreach($row as $k => $v) {
    			$_row[$headers[$k]] = $v;
    		}
    		$data[] = $_row;
    	}
    	
    	return $data;
    }
}