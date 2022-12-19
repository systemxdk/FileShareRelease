<?php

class DateAssistant {
    
    static function render_date_dk($row, $value) {
        return $value != "" ? date("d.m.Y H:i", strtotime($value)) : "";
    }
}