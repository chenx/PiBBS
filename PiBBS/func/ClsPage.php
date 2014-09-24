<?php
//
// Provide paging function.
// 
// @author: Xin Chen
// @Created on: 8/26/2013
// @Last modified: 8/27/2013
//

class ClsPage {
    
    private $pageSize;
    private $pageButtonCount;
    private $totalCount;
    private $pageCount;
    private $currentPage;
    private $BaseUrl; 
    private $showTotal;
    private $showGoToPage;
    private $param_page;
    private $LANG;
    
    /**
    * Default constructor assigns needed env variables.
    *
    * @param totalCount - total number of records.
    * @param curPage    - current page.
    * @param pageSize   - number of records on one page.
    * @param pageButtonCount - number of paging buttons on one page.
    * @param pg - the url parameter name for page number.
    * @param lang - language.
    */  
    public function __construct($totalCount, $curPage, $pageSize=20, $pageButtonCount=10, $pg="p", $lang = "en") {
        $this->pageSize = $pageSize;
        $this->pageButtonCount = $pageButtonCount;  // number of paging buttons.
        
        $this->totalCount = $totalCount;
        $this->pageCount = ceil($totalCount / $this->pageSize);
        if ($this->pageCount == 0) $this->pageCount = 1; // happens when totalCount = 0.
        
        $this->currentPage = $curPage;
        if ($this->currentPage == "") { $this->currentPage = 0; }
        else if ($this->currentPage < 0) { $this->currentPage = 0; }
        else if ($this->currentPage >= $this->pageCount) { $this->currentPage = $this->pageCount - 1; }
        
        // Base URL used by page links. Page parameter should be at the end. E.g. "index.php?$pg="
        $baseUrl = $_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING'];
        if (preg_match("#$pg=[0-9]*$#", $baseUrl) > 0) {
          $this->BaseUrl = preg_replace("#$pg=[0-9]*$#", "", $baseUrl) . "$pg=";
        } else if ( empty($_SERVER['QUERY_STRING']) ) {
          $this->BaseUrl = $baseUrl . "$pg=";
        } else {
          $this->BaseUrl = $baseUrl . "&$pg=";
        }

        //print "total: $this->totalCount, pagecount: $this->pageCount, curpage: $this->currentPage<br>";
        $this->showTotal = true;
        $this->showGoToPage = true;
        $this->param_page = $pg; // The url parameter used for page.
        $this->LANG = $lang;
    }

    /**
    * Get the index of the starting record on the current page.
    */
    public function getStart() { return $this->currentPage * $this->pageSize + 1; }


    /**
    * Get the index of the ending record on the current page.
    */
    public function getEnd() { return (1 + $this->currentPage) * $this->pageSize; }


    /**
    * Control whether to output the "Total" value in the output navigation bar.
    *
    * @param $val - true/false.
    */
    public function setShowTotal($val) { $this->showTotal = $val; }

    public function setShowGotoPage($val) { $this->showGoToPage = $val; }

    public function showGotoPage() {
        if ($this->LANG == "cn") {
            $txt_title = "输入前往的页数";
            $btn_value = "前往";
            $btn_title = "点击前往指定页";
        } else {
            $txt_title = "The page to go to";
            $btn_value = "Go";
            $btn_title = "Click to go to specified page";
        }

        $s = <<<EOF
 <input type='text' id='gotoPage' name='gotoPage' value='' style='width:20px;' maxlength='10' title='$txt_title'>
<input type='button' name='btnGoto' value='$btn_value' onclick="javascript: jumpToTopic($this->pageCount, '$this->param_page');" title='$btn_title'>
EOF;
        return $s;
    }


    function writeNavBar($T_page = "Page", $T_count = "Count") {
        $PageCount = $this->pageCount;
        $CurrentPageIndex = $this->currentPage;
        $PageButtonCount = $this->pageButtonCount;
        $baseUrl = $this->BaseUrl;

        $DEBUG = 0;
        $lblNext = "&gt;&gt;"; //"Next";
        $lblPrev = "&lt;&lt;"; // "Prev";
        $lblFirst = "First";
        $lblLast = "Last";

        $s = "$T_page: "; // "Page: ";

        if ($DEBUG) {
            print "pagecount: $PageCount, currentPageIndex: $CurrentPageIndex, ";
            print "PageButtonCount: $PageButtonCount<br>";
        }

        $startPage = (floor(($CurrentPageIndex)/$PageButtonCount) * $PageButtonCount);
        if ($DEBUG) print "startpage = $startPage<br>";

        $tmp = $PageCount - $PageButtonCount;
        if ($tmp > 0 && $tmp < $startPage) { $startPage = $tmp; }

        // Prev.
        if ($CurrentPageIndex == 0) { $s .= $lblPrev . " "; }
        else
        {
            $j = $CurrentPageIndex - 1;
            $s .= "<a href=\"" . $baseUrl . $j . "\">" . $lblPrev . "</a> ";
        }

        if ($CurrentPageIndex >= $PageButtonCount) { $s .= "<a href=\"" . $baseUrl . "0\">1</a> "; }

        // ...
        if ($startPage > 1) { $s .= "<a href=\"" . $baseUrl . ($startPage - 1) . "\">...</a> "; }

        for ($i = $startPage, $len = min($PageCount, $startPage + $PageButtonCount); $i < $len; ++ $i) {
            if ($i == $CurrentPageIndex) { $s .= " " . (1 + $i); }
            else { $s .= " <a href='" . $baseUrl . $i . "'>". (1 + $i) . "</a>"; }
        }

        // ...
        if ($startPage + $PageButtonCount < $PageCount - 1) {
            $j = $PageButtonCount + $startPage;
            $s .= " <a href=\"" . $baseUrl . $j . "\">...</a> ";
        }

        if ($startPage + $PageButtonCount <= $PageCount - 1) {
            $s .= " <a href=\"" . $baseUrl . ($this->pageCount - 1) . "\">$this->pageCount</a> "; 
        }

        // Next.
        if ($CurrentPageIndex >= $PageCount - 1) { $s .= " " . $lblNext; }
        else
        {
            $j = $CurrentPageIndex + 1;
            $s .= " <a href=\"" . $baseUrl . $j . "\">" . $lblNext . "</a>";
        }

        //if ($this->showTotal) { $s .= " [$this->totalCount records, $this->pageCount pages]"; }
        //if ($this->showTotal) { $s .= " | Topics: $this->totalCount "; }
        if ($this->showTotal) { $s = "$T_count: $this->totalCount | $s"; }

        if ($this->showGoToPage) { $s .= $this->showGotoPage(); }

        return $s;
    }
}

?>

