 <?
 /**
     * This function gives you the next working days based on the buffer
     *
     * @param $date must be in YYYY-MM-DD format
     * @param int $buffer
     * @param string $holidays - You can pass either an array of holidays in YYYY-MM-DD format or a URL for a .ics file
     *        containing holidays this defaults to the UK govt holiday data for England and Wales
     * @return string
     */
    
    function getWorkingDays($date,$buffer=1,$holidays='') {
        if ($holidays==='') $holidays = 'https://www.gov.uk/bank-holidays/england-and-wales.ics';

        if (!is_array($holidays)) {
            $ch = curl_init($holidays);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
            $ics = curl_exec($ch);
            curl_close($ch);
            $ics = explode("\n",$ics);
            $ics = preg_grep('/^DTSTART;/',$ics);
            $holidays = preg_replace('/^DTSTART;VALUE=DATE:(\\d{4})(\\d{2})(\\d{2}).*/s','$1-$2-$3',$ics);
        }

        $addDay = 0;
        while ($buffer--) {
            while (true) {
                $addDay++;
                $newDate = date('Y-m-d', strtotime("$date +$addDay Days"));
                $newDayOfWeek = date('w', strtotime($newDate));
                if ( $newDayOfWeek>0 && $newDayOfWeek<6 && !in_array($newDate,$holidays)) break;
            }
        }

        return $newDate;
    }

?>