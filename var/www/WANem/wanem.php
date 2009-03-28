<?
        include("config.inc.php")
?>


<html><head><title>TCS  WANEM</title>
</head>
<body bgcolor="#FFE87C">
<?php
                //$ip=$_REQUEST['pc'];
                $command=$wanchar_DIR."/tcs_wanem_main.sh";

                $output=shell_exec($command." 2>&1");  //system call

                print "<br>";

		print "<pre>$output</pre>";

                //if(strlen($output) < 5) {

                  //  print("<br><br><b><font color=red>Remote Host Not Reachable !!</font></b>");
                //}
                //else {

                  //      $left=explode(",",$output);

                        //print $output;
                    //    print "<table align=center border=1 cellspacing=1 cellpadding=10>";
                      //  for($i=0,$j=1; $left[$i]; $i=$i+2,$j=$j+2) {

                        //        if($i % 4 == 0)
                          //              $bgcol = "pink";
                            //    else
                              //          $bgcol = "white";

                                //print "<tr bgcolor=$bgcol>";
                                //print "<td align=left><font color=blue size=3><b>$left[$i]</b></font></td>";
                                //print "<td align=left><font color=blue size=3><b>$left[$j]</b></font></td>";

                                //print "</tr>";
                       // }
                       // print "</table>";
                  //}
 
?>
</center></body></html>
