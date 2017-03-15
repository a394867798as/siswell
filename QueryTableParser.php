<?php
/**
 * class QueryTableParser
*
* Very rough class to extract table names from a SQL query.
*
* This class simply looks for specific tokens like FROM, JOIN, UPDATE, INTO
* and collects a list of the very next token after those words.
*
* It doesn't attempt to parse aliases, or any other query structure.
*
* This probably doesn't handle table names with a space in it like `table name`
*
* @author Gavin Towey <gavin@box.com>
* @created 2012-01-01
* @license Apache 2.0 license.  See LICENSE document for more info
*
* @todo handle table names with spaces wrapped in backticks or quotes
* @todo stop parsing early if possible -- after the JOIN clause (if any)
* @todo ignore token values inside string literals or backticks
*/
class QueryTableParser {

    public $pos;
    public $query;
    public $len;
    public $checkTokens = false;
    public $table_tokens = array(
            'from',
            'join',
            'update',
            'into',
    );

    /**
     * parse a query and return an array of table names from it.
     *
     * @param string $query     the sql query
     * @return array    the list of table names.
     */
    public function parse($query) {
        $query = preg_replace("/\s?,\s?/s", ", ", $query);
        echo $query;
        $this->query = preg_replace("/\s+/s", " ", $query);
        $this->pos = 0;
        $this->len = strlen($this->query);
        #print "<pre>";
        #print "parsing {$this->query}; length {$this->len}\n";


        $tables = array();
        while ($this->has_next_token()) {
            $token = $this->get_next_token();
            #print "--> found $token\n";
            if($token == "limit"){
                $this->checkTokens = false;
            }
            if (in_array(strtolower($token), $this->table_tokens)) {
                $this->checkTokens = false;
                $table = $this->get_next_token();

                #Handles old style joins
                if (!preg_match("/,/", $table)) {
                    $this->checkTokens = true;
                }
                $tables = $this->checkOldStyleJoins($table, $tables);
            }else if($this->checkTokens && preg_match("/,/", $token)){
                $table = $this->get_next_token();
                $tables = $this->checkOldStyleJoins($table, $tables);
            }
        }
        #print "</pre>";

        return array_keys($tables);
    }
    /*Handles old style joins*/
    private function checkOldStyleJoins($table, $tables){
        if (preg_match("/,/", $table)) {
            while (preg_match("/,/", $table)) {
                $table = str_replace(',', '', $table);
                if (preg_match("/\w+/", $table)) {
                    $table = str_replace('`', '', $table);
                    $tables[$table]=1;
                }
                $table = $this->get_next_token();
            }
            if (preg_match("/\w+/", $table)) {
                $table = str_replace('`', '', $table);
                $tables[$table]=1;
            }
        }
		elseif (preg_match("/(/", $table)){
			    return $tables;
		}
        else {
            if (preg_match("/\w+/", $table)) {
                $table = str_replace('`', '', $table);
                $tables[$table]=1;
            }
        }

        return $tables;
    }
    /**
     * return true if we're not at the end of the string yet.
     * @return boolean true if there are more tokens to read
     */
    private function has_next_token() {
        // at end
        if ($this->pos >= $this->len) {
            return false;
        }
        return true;
    }

    /**
     * returns the next whitespace separated string of characters
     * @return string   the token value
     */
    private function get_next_token() {
        // get the pos of the next token boundary
        $pos = strpos($this->query, " ", $this->pos);
        #print "get next token {$this->pos} {$this->len} {$pos}\n";
        if ($pos === false) {
            $pos = $this->len;
        }

        // found next boundary
        $start = $this->pos;
        $len = $pos - $start;
        $this->pos = $pos + 1;
        return substr($this->query, $start, $len);
    }

}
$sql = "SELECT a,b FROM table as a,table2 as b on b.cui=a.cud ,table3 where a.id=b.uid and a.id in(select c.id from table4 as c) group by a.cid limit 0,10";
$queryTable = new QueryTableParser();
$tables = $queryTable->parse($sql);
var_dump($tables);
?>
