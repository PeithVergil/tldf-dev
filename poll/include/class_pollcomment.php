<?php
/**
 * ----------------------------------------------
 * URL: http://www.pilotgroup.net
 * ----------------------------------------------
 */

class pollcomment extends poll {

    var $comment_form_html;
    var $poll_comment_html;
    var $comment_data;
    var $comment_records;
    var $comment_tpl;
    var $comment_order;
    var $comment_index;
    var $total_comments;    
    var $form_fields;
    var $form_message;
    var $date_format;
    var $word_length;

    function pollcomment() {
        global $_GET, $_POST;
        $this->comment_form_html = array();
        $this->poll_comment_html = array();
        $this->comment_index = '';
        $this->comment_data = array();
        $this->comment_records = '';
        $this->comment_tpl = '';
        $this->comment_order = '';
        $this->total_comments = array();
        $this->form_message = array();
        $this->date_format = "m/d/Y H:i";
        $this->word_length = 35;
        
        $this->comment_index = (isset($_GET['c_page'])) ? trim($_GET['c_page']) : 0;
        $this->comment_index = (isset($_POST['c_page'])) ? trim($_POST['c_page']) : $this->comment_index;
        if (empty($this->comment_index) || $this->comment_index<0) {
            $this->comment_index = 0;
        }
        $this->form_fields = array("user_id","message");
        $this->poll();
    }

    function set_comments_per_page($records) {
        if (is_integer($records) && $records>0) {
            $this->comment_records = $records;
            return true;
        } else {
            return false;
        }
    }

    function set_form_error($error_msg_arr) {
        for ($i=0; $i<sizeof($this->form_fields); $i++) {
            if (isset($error_msg_arr[$this->form_fields[$i]]) && !empty($error_msg_arr[$this->form_fields[$i]])) {
                $error_msg_arr[$this->form_fields[$i]] = trim($error_msg_arr[$this->form_fields[$i]]);
                $this->form_message[$this->form_fields[$i]] = $error_msg_arr[$this->form_fields[$i]];
            }
        }
    }

    function get_poll_comments($poll_id) {
        if (!isset($this->comment_data[$poll_id])) {
            $record = ($this->comment_records>0) ? $this->comment_records : $this->pollvars['entry_pp'];            
            if (!isset($this->total_comments[$poll_id])) {
                $this->get_total_comments($poll_id);
            }
            $pages = (int) ($this->total_comments[$poll_id]/$record);
            if (($this->comment_index>$pages*$record) && $pages>0) {
                $this->comment_index = $this->total_comments[$poll_id]-$record;
            }   
            $this->db->query("SELECT * FROM ".$this->tbl['poll_comment']." WHERE (poll_id = '$poll_id') ".$this->comment_order." LIMIT ".$this->comment_index.",$record");            
            if ($this->total_comments[$poll_id]>0) {
                for ($i=0; $i<$record; $i++) {
                    if ($this->db->fetch_array($this->db->result)) {
                        $option_time_arr[] = $this->db->record['time'];
                        $option_host_arr[] = $this->db->record['host'];
                        $option_browser_arr[] = $this->db->record['browser'];
                        $option_name_arr[] = $this->db->record['name'];
                        $option_email_arr[] = $this->db->record['email'];
                        $option_message_arr[] = $this->db->record['message'];
                    } else {
                        break;
                    }
                }
                $comment_fields = array("time","host","browser","name","email","message");
                for($i=0;$i<sizeof($comment_fields);$i++) {
                    $field = "option_".$comment_fields[$i]."_arr";
                    $this->comment_data[$poll_id][$comment_fields[$i]] = $$field;
                }
            } else {
                $this->comment_data[$poll_id] = '';
            }
        }
        return $this->comment_data[$poll_id];
    }

    function data_order_by($by, $order) {
        if (($by != "time") && ($by != "name") && ($by != "email") && ($by != "host") && ($by != "browser") && ($by != "message")) {
            $by = "time";
        }
        switch ($order) {
            case "asc":
                $this->comment_order = "ORDER BY $by ASC";
                break;
            case "desc":
                $this->comment_order = "ORDER BY $by DESC";
                break;
            default:
                $this->comment_order = "";
                return false;
        }
        return true;
    }

    function get_total_comments($poll_id) {
        if (!isset($this->total_comments[$poll_id])) {
            $this->db->fetch_array($this->db->query("SELECT COUNT(*) AS total FROM ".$this->tbl['poll_comment']." WHERE (poll_id = '$poll_id')"));
            $this->total_comments[$poll_id] = $this->db->record["total"];
            return $this->total_comments[$poll_id];
        } else {
            return $this->total_comments[$poll_id];
        }
    }

    function get_pages($total_records, $current_index, $records_per_page, $page_name, $max_pages=10, $separate=" | ") {
        $pages_html = '';
        if ($total_records>0) {            
            $append = $this->get_query_strg($page_name);
            $remain = $total_records % $records_per_page;                
            $i = $total_records-$remain;
            $pages = (int) ($total_records/$records_per_page);
            $show_max = ($max_pages<$pages && $max_pages>0) ? $max_pages : $pages;                
            $index = $current_index;
            if (($current_index>($total_records-$show_max*$records_per_page)) && $pages>0) {
                $index = $total_records-$show_max*$records_per_page;
            }
            for ($k=0; $k<$pages; $k++) {
                $pages_arr[] = $k*$records_per_page+$remain;   
            }
            if ($pages>0 && $records_per_page!=$total_records) {
                if (($current_index > ($total_records-$max_pages*$records_per_page)) && ($total_records-$max_pages*$records_per_page)>0) {
                    $index = $total_records-(($max_pages-1)*$records_per_page);
                }
                $next_page = $current_index+$records_per_page;
                $prev_page = $current_index-$records_per_page;                   
                $prev_page = ($prev_page<0 && $pages>0) ? 0 : $prev_page;
                if ($prev_page >= 0 && $current_index>0) {
                    $pages_html .= "<a href=\"$append"."$page_name=$prev_page\">&lt;</a>&nbsp;";
                }
                if ($index > ($total_records-$pages*$records_per_page)) {
                    $index -= $records_per_page;     
                }                    
                $current_page = (int) ($index / $records_per_page);
                for ($j=1; $j<=$show_max; $j++) {
                    $page_number = $current_page + $j;                       
                    $position = $page_number-1;
                    if ($position >= sizeof($pages_arr)) {
                        $position = sizeof($pages_arr)-1;
                    }
                    $pages_html .= " <a href=\"$append"."$page_name=$pages_arr[$position]\">$page_number</a>$separate";
                }
                if ($next_page < $total_records) {
                    $pages_html .= "&nbsp;<a href=\"$append"."$page_name=$next_page\">&gt;</a>";
                } 
            }   
        }
        return $pages_html;
    }

    function get_comment_pages($poll_id, $max_pages=10, $separate=" | ") {
        if (!isset($this->comment_pages_html[$poll_id])) {
            $record = ($this->comment_records>0) ? $this->comment_records : $this->pollvars['entry_pp'];
            if (!isset($this->total_comments[$poll_id])) {
                $this->get_total_comments($poll_id);
            }
            if ($this->total_comments[$poll_id]>0) {
                $this->comment_pages_html[$poll_id] = $this->get_pages($this->total_comments[$poll_id], $this->comment_index, $record, "c_page", $max_pages, $separate);        
            } else {
                $this->comment_pages_html[$poll_id] = '';
            }
        }
        return $this->comment_pages_html[$poll_id];        
    }

    function set_date_format($date_strg) {
        if (!empty($date_strg)) {
            $this->date_format = $date_strg;
            return true;
        } else {
            return false;
        }
    }

    function view_poll_comments($poll_id) {

        global $config;
		
        if (!isset($this->poll_comment_html[$poll_id]) || !isset($this->poll_comment_html[$poll_id][$this->comment_tpl])) {
            $row = $this->db->fetch_array($this->db->query("SELECT template FROM ".$this->tbl['poll_tpl']." WHERE (title = '".$this->comment_tpl."' and tplset_id='0')"));
            $row['template'] = ereg_replace("\"", "\\\"", $row['template']);
            $display_html = '';
            if (!isset($this->comment_data[$poll_id])) {
                $this->get_poll_comments($poll_id);
            }
            if (is_array($this->comment_data[$poll_id])) {
                for ($i=0;$i<sizeof($this->comment_data[$poll_id]['time']);$i++) {
                    $data['time'] = date($this->date_format,$this->comment_data[$poll_id]['time'][$i]+$this->pollvars['time_offset']*3600);
                    $data['host'] = $this->comment_data[$poll_id]['host'][$i];
                    $data['browser'] = htmlspecialchars($this->comment_data[$poll_id]['browser'][$i]);
                    $data['name'] = htmlspecialchars($this->comment_data[$poll_id]['name'][$i]);
                    $data['email'] = $this->comment_data[$poll_id]['email'][$i];                    
                    $data['message'] = $this->strip_new_lines($this->comment_data[$poll_id]['message'][$i]);
                    $data['message'] = $this->wrap_text($data['message'],$this->word_length);
                    $data['message'] = htmlspecialchars($data['message']);
                    $data['message'] = nl2br($data['message']);                   
                    eval("\$display_html .= \"$row[template]\";");
                }
                $this->poll_comment_html[$poll_id][$this->comment_tpl] = $display_html;
            } else {
                $this->poll_comment_html[$poll_id][$this->comment_tpl] = '';
            }    
        }
        return $this->poll_comment_html[$poll_id][$this->comment_tpl];
    }

    function set_template($title) {
        if (!empty($title)) {
            $this->db->fetch_array($this->db->query("SELECT * FROM ".$this->tbl['poll_tpl']." WHERE title='$title'"));
            if ($this->db->record) {
                $this->comment_tpl = $title;
                return true;
            } else {
                $this->comment_tpl = "";
                return false;
            }
        } else {
            return false;
        }
    }

    function format_string($strg) {
        if (!get_magic_quotes_gpc()) {
            $strg = addslashes($strg);
        }
        $strg = trim($strg);
        return $strg;
    }

    function wrap_text($strg, $max_word_length=60, $break="\n") {
        if (!empty($strg)) {
            $max_word_length += 1;
            preg_match_all("/(\S{".$max_word_length.",})/", $strg, $matches_arr);
            if (isset($matches_arr[1]) && sizeof($matches_arr[1])>0) {
                for ($i=0; $i<sizeof($matches_arr[1]); $i++) {
                    $break_x = (int) (strlen($matches_arr[1][$i])/$max_word_length);
                    $new_word = '';
                    for ($k=0; $k<$break_x; $k++) {
                        $new_word .= substr($matches_arr[1][$i], ($k*$max_word_length), $max_word_length).$break;
                    }
                    $new_word .= substr($matches_arr[1][$i], -(strlen($matches_arr[1][$i])-$break_x*$max_word_length));
                    $strg = str_replace($matches_arr[1][$i], $new_word, $strg);
                }
            }
        }
        return $strg;
    }
    
    function strip_new_lines($strg,$max_new_lines=2) {
        $strg = str_replace("\r\n", "\n", $strg);
        $strg = str_replace("\r", "\n", $strg);
        $replace = str_repeat ("\n", $max_new_lines);
        $strg = preg_replace("/\n{".$max_new_lines.",}/", $replace, $strg);
        return $strg;
    }

    function add_comment($poll_id) {
        global $_POST;
        for ($i=0; $i<sizeof($this->form_fields); $i++) {
            $field_name = $this->form_fields[$i];
            if (isset($_POST[$field_name])) {
                $$field_name = $this->format_string($_POST[$field_name]);
            } else {
                $$field_name = '';
            }
        }
        global $GUEST_USER_ID;
        if (empty($user_id)) $user_id = $GUEST_USER_ID; 
        
        $this_time = time();
        $host = @gethostbyaddr($this->ip);
        $agent = @getenv("HTTP_USER_AGENT");
        $this->db->query("INSERT INTO ".$this->tbl['poll_comment']." (poll_id,time,host,browser,user_id,message) VALUES ('$poll_id','$this_time','$host','$agent','$user_id','$message')");
        return ($this->db->result) ? true : false;
    }

    function print_message($strg,$autoclose=0) {
        $msg ='';
        if ($autoclose==1) {
            $msg .= "<script language=\"JavaScript\">
            setTimeout(\"closeWin()\",2000);
            function closeWin() {
                self.close();
            }
            </script>";
        }
        $msg .= "<font face=\"Verdana, Arial, Helvetica, sans-serif\" size=\"1\">$strg</font>";
        return $msg;
    }

    function is_comment_allowed($poll_id) {
        if ($poll_id>0) {
            $this->db->fetch_array($this->db->query("SELECT comments FROM ".$this->tbl['poll_index']." WHERE poll_id=$poll_id AND status<2"));
            return ($this->db->record['comments']==1) ? true : false;
        } else {
            return false;
        }
    }

    function poll_form($poll_id,$msg='') {

        global $config;
		
        global $_POST;
        if (!isset($this->comment_form_html[$poll_id]) || !isset($this->comment_form_html[$poll_id][$this->comment_tpl])) {            
            $question = $this->get_poll_question($poll_id);
            for ($i=0; $i<sizeof($this->form_fields); $i++) {
                if (isset($_POST[$this->form_fields[$i]]) && !empty($msg)) {
                    $comment[$this->form_fields[$i]] = stripslashes(htmlspecialchars($this->format_string($_POST[$this->form_fields[$i]])));
                } else {
                    $comment[$this->form_fields[$i]] = '';
                }
            }
            if (isset($this->comment_tpl) && !empty($this->comment_tpl)) {
                $row = $this->db->fetch_array($this->db->query("SELECT template FROM ".$this->tbl['poll_tpl']." WHERE (title = '".$this->comment_tpl."' and tplset_id='0')"));
                $row['template'] = ereg_replace("\"", "\\\"", $row['template']);
            } else {
                $row['template'] = $this->get_poll_tpl("comment");
            }
            eval("\$result_html = \"".$row['template']."\";");
            $this->comment_form_html[$poll_id][$this->comment_tpl] = $result_html;
        }
        return $this->comment_form_html[$poll_id][$this->comment_tpl];
    }

    function comment_process($poll_id) {
        global $_POST, $_GET;
        if (isset($_GET['action']) || isset($_POST['action'])) {
            $action = (isset($_POST['action'])) ? trim($_POST['action']) : trim($_GET['action']);
        } else {
            $action = '';
        }
        if (isset($_POST['pcomment']) && $_POST['pcomment']>0) {
            $pcomment = $_POST['pcomment'];    
        } else {
            $pcomment = '';
        }    
        if (!isset($poll_id)) {
            $msg = "Poll ID <b>".$poll_id."</b> does not exist or is disabled!";
            return $this->poll_form($poll_id,$msg);
        } else {
            $msg = '';
        }
        if ($action == "add" && $this->is_comment_allowed($poll_id) && $poll_id==$pcomment) {
            for (reset($this->form_message); $key=key($this->form_message); next($this->form_message)) {
                if (!empty($this->form_message[$key])) {
                    if (isset($_POST[$key])) {
                        $_POST[$key] = trim($_POST[$key]);
                    }
                    if (!isset($_POST[$key]) || empty($_POST[$key])) {
                        $msg = $this->form_message[$key];
                        break;
                    }   
                }
            }
            if ($msg == "") {
                $this->add_comment($poll_id);
            }
        }
        return $this->poll_form($poll_id,$msg);
    }

}

?>