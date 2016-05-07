<?php

class HTML
{

    public function date($date, $time = false)
    {
        if(empty($date))
            return null;

        if($time)
            list($date, $time) = explode(' ', $date);

        list($year, $month, $day) = explode('-', $date);
        $monthes = array('января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря');
        $m = (int)$month;

        return $day . ' ' . $monthes[$m - 1] . ' ' . $year;
    }

    public function get_correct_str($num, $str1, $str2, $str3)
    {
        $val = $num % 100;

        if($val > 10 && $val < 20) return $num . ' ' . $str3;
        else{
            $val = $num % 10;
            if($val == 1) return $num . ' ' . $str1;
            elseif($val > 1 && $val < 5) return $num . ' ' . $str2;
            else return $num . ' ' . $str3;
        }
    }

    public function price($price)
    {
        if(is_int($price))
            return $price;

        return number_format($price, 1, ',', '');
    }

    public function option($json, $key)
    {
        $data = json_decode($json);
        if(isset($data->$key))
            return $data->$key;

        return null;
    }

    public function shield($value)
    {
        $shields = array('light', 'dark');
        echo $shields[$value];
    }

    public function bgSize($image)
    {
        if($image->center == 1)
            echo 'auto 100%';
        else
            echo 'cover';
    }

    public function parsePhone($item)
    {
        $phone = '';
        preg_match_all('/(\d)/', $item, $matches);
        foreach($matches[0] as $number)
            $phone .= $number;
        return $phone;
    }

}

?>