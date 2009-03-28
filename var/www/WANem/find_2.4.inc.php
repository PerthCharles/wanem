<?
/****************************************************************************/
/*                              TCS WANem                                   */                 
/****************************************************************************/
/*                   COPYRIGHT (c) 2007 TCS All Rights Reserved             */
/* 
/* 
/* WANem is a WAN emulation tool Conceptualized and developed by Innovation */
/* LAB TCS. We thank the open source community as we have taken the inspira */
/* tion from the Netem, the open source network emulator.The GUI is also    */
/* a modified version of Netem GUI developed by British Telecom where new   */
/* features are added and the GUI is expanded.                              */
/*                                                                          */
/*                                                                          */
/****************************************************************************/
/****************************************************************************/
/*   Author       : Manoj Nambiar, TCS Innovation Lab Performance Engg.                                             
/*   Date         : March 2007                                              */
/*   Synopsis     :                                                         */
/*   Description  :                                                         */
/*                                                                          */
/*   Modifications:                                                         */
/****************************************************************************/
/*                                                                          */

//**************************************************************************************
//Function to look for bridges on the machine and create a bridge select box if any were
//found.
//**************************************************************************************
function find_bridges($bridgeName, $bridgeInts, $showBridges) {
	//Use the 'brctl show' command to look for any existing bridges
	global $brctl_CMD;
	$output=shell_exec($brctl_CMD.' show');

	//****Set and reset initial variables and arrays****
	$lines=0; //Number of lines in the 'brctl show' output
	$selectHTML=""; //The html that will be generated. This will be html to create
					//a select box with all bridges that were found as the values.

	//****Get the number of lines and the starting character of each line****
	//Loop through all characters in $output
	for ($i = 0; $i <= strlen($output); $i++) {
		//Check each character in turn to see if it has an ascii value of 10
		//ascii 10 = linefeed character
		if (ord(substr($output, $i, 1)) == 10) {
			//if a linefeed character was found then add 1 to lines
			$lines=$lines+1;
			//Add the starting character of the next line to the array $lineStart
			$lineStart[] = $i+1;
		}
	}

	//****Get the bridge names and the interfaces that they use****
	//Check if there's more than 1 line in the output
	//If there's only one line then no bridges are present
	if ($lines>1) {
		//Set $i to second line
		$i = 2;

		//loop through all lines starting from the second line
		while ($i <= $lines):
			$n = $lineStart[($i-2)]; //Set $n to the first character of the current line
			$tmpStr=""; //For temporarily holding a string

			//Loop through the characters of the bridge name.  If a tab character is found
			//then exit the loop and write the bridge name to the $bridgeName[] array.
			while (ord(substr($output, $n, 1)) != 9):
			//Add current character to tempStr
				$tmpStr=$tmpStr . substr($output, $n, 1);
				//increment $n
				$n= ++$n;
			endwhile;
			//Add bridge name to $bridgeName array
			$bridgeName[]=$tmpStr;

			//Clear $tmpStr
			$tmpStr="";

			//Move $n to the beginning of where the first interface name should be.
			//There are 5 tab characters between bridge name and interface name

			//Set $tabs to zero, this is a variable to count the number of tabs found
			$tabs=0;

			while ($tabs < 5):
				//Check for a tab character
				if (ord(substr($output, $n, 1)) == 9) {
					//Add 1 to $tabs
					$tabs = ++$tabs;
				}
				//increment $n
				$n = ++$n;
			endwhile;

			//Now $n is at the correct position for checking the interface names.
			//Check if there's 1 or more interface names and add them to the
			//$bridgeInts[] array, if no interface names are found then just add a blank
			//space to the $bridgeInts[] array

			//Set $intStr to empty initially
			$intStr="";

			//If $n is not a linefeed character..
			if (ord(substr($output, $n, 1)) != 10) {
				//Get first interface name
				while (ord(substr($output, $n, 1)) != 10):
					//Add current character to tempStr
					$tmpStr=$tmpStr . substr($output, $n, 1);
					//increment $n
					$n= ++$n;
				endwhile;

				//Add first interface name to $intStr
				$intStr=$tmpStr;

				//Clear $tmpStr
				$tmpStr="";

				//increment $n to get past the linefeed character
				$n = ++$n;

				//Set the $lastInt flag to FALSE, this will be set to TRUE if there are no further
				//interfaces
				$lastInt=FALSE;
				while ($lastInt==FALSE):
					//If the next character is a tab then that means there's another
					//interface
					if (ord(substr($output, $n, 1)) == 9) {
						//increment $n by 7 because there's 7 tabs before the start of
						//the second interface name
						$n=($n+7);

						//Get next interface name
						while (ord(substr($output, $n, 1)) != 10):
							//Add current character to tempStr
							$tmpStr=$tmpStr . substr($output, $n, 1);
							//increment $n
							$n= ++$n;
						endwhile;
						//Add a separator (#) and the next interface name to $intStr
						$intStr=$intStr . "#" . $tmpStr;

						//If $n+1 is lower than the length of $output then there may be
						//another interface, so keep $lastInt as FALSE, add 1 to $n and go
						//back through the while loop.  Otherwise just set $lastInt to
						//TRUE
						if (($n+1)< strlen($output)) {
							$n= ++$n;
						} else {
							$lastInt=TRUE;
						}
					} else {
						$lastInt=TRUE;
					}
				endwhile;

				//Add interface names to $bridgeInts[] array
				$bridgeInts[]=$intStr;

				//increment $i to get to next bridge line, $i will be incremented
				//again after this so $i will have been incremented twice
				$i = ++$i;
			}
			//Add a blank space to $bridgeInts[] if no interfaces were found
			if (empty($intStr)) {
				$bridgeInts[]=" ";
			}
			//increment $i
			$i = ++$i;
		endwhile;
	}

	//If a bridge has been selected by the user then show the name of the bridge
	//and an 'Unselect bridge' button.
	if ($_SESSION[bridgeSelected] != "" & $showBridges==1) {
?>
		<div align=center style="color: #000000; background-color: #7799ff; border: thin solid #000000; width: 100%">
		<table border="0" width="100%">
		  <tr>
			<td width="100%">
			  <p align="center"><b>Selected bridge: <? echo $_SESSION[bridgeSelected]; ?></b>
			  <input type="submit" value="Unselect bridge" name="btnUnselectBridge">
			</td>
		  </tr>
		</table>
		</div>
<?
	} else {

		//****If bridge(s) exist then generate html for a bridge selectbox****
		//Add the bridges in reverse order so they display in creation order
		//Check that at least one bridge was found
		if ((empty($bridgeName[0]))==FALSE & $showBridges==1) {
?>
		<div align=center style="color: #000000; background-color: #0070C0; border: thin solid #000000; width: 970">
		<table border="0" width="100%">
		  <tr>
<?
			//Create HTML for a select box within a table cell and add the last bridge to it
			$selectHTML="\t\t\t" . '<td width="100%">'  . "\n\t\t\t  " . '<p align="center"><b>Bridges</b>' . "\n\t\t\t  " . '<select size="1" name="bridges">' . "\n\t\t\t\t" . '<option selected>' . $bridgeName[(count($bridgeName)-1)] . '</option>' . "\n";

			//Loop through all remaining bridges in the array from 2nd-from-last to first
			if (count($bridgeName)>1) {
				$i=(count($bridgeName)-1);
				while ($i >= 1):
					//If a bridge name exists then add it to the select box.
					if ((empty($bridgeName[($i-1)]))==FALSE) {
						$selectHTML=$selectHTML . "\t\t\t\t" . '<option>' . $bridgeName[$i-1] . '</option>' . "\n";
					}
					//decrement $i
					$i = --$i;
				endwhile;
			}
			//Add a submit button
			$selectHTML=$selectHTML . "\t\t\t  " . '</select>' . "\n\t\t\t  " . '<input type="submit" value="Select bridge" name="btnSelectBridge">' . "\n\t\t\t" . '</td>' . "\n\t\t  " .  '</tr>' . "\n\t\t" . '</table>' . "\n\t\t" . '</div>' . "\n";

			//Display the select box and the button
			echo $selectHTML;
		}
?>
		</td></tr>
		</table>
		</div>
<?
	}
}

//**************************************************************************************
//Function to look for interfaces on the machine and create a select box with all of
//the interfaces that were found.  Uses ifconfig.
//The function also checks if the 'select bridge' button was pressed and selects the
//correct interfaces for the selected bridge if the button was pressed.
//**************************************************************************************
function find_interfaces($interfaces, $bridgeName, $bridgeInts) {

	global $ifconfig_CMD;
	//Get the output of the ifconfig command
	$output=shell_exec($ifconfig_CMD);

	//Check for an empty value of output.  This means no interfaces are running.
	//This should not be possible but check for it just in case.
	if (empty($output) OR strpos($output, "command not found")) {
		//Display the fact that no interfaces were found.
		echo '<strong>No interfaces found! (Maybe ifconfig is not installed on the machine)</strong>';
		exit();
	}

	//Set $lines to zero
	$lines=0;

	//****Get the number of lines and the starting character of each line****
	//Loop through all characters in $output
	for ($i = 0; $i <= strlen($output); $i++) {
		//Check each character in turn to see if it has an ascii value of 10
		//ascii 10 = linefeed character
		if (ord(substr($output, $i, 1)) == 10) {
			//if a linefeed character was found then add 1 to lines
			$lines=$lines+1;
			//Add the starting character of the next line to the array $lineStart
			$lineStart[] = $i+1;
		}
	}
	//Subtract 1 from $lines to get the correct number of lines
	$lines=$lines-1;

	//****Get the interface names****

	//Set $i to first line
	$i = 1;

	//loop through all lines
	while ($i <= $lines):
		//If on the first line then set $n to zero
		if ($i==1) {
			$n=0;
		} else {
			$n = $lineStart[($i-2)]; //Set $n to the first character of the current line
		}
		$tmpStr=""; //For temporarily holding a string

		//Check the first character of the line for either a linefeed, a space, or
		//something other than those two.
		switch (ord(substr($output, $n, 1))) {
			case "10": //Character is a linefeed
				//increment $i
				$i = ++$i;
			break;

			case "32": //Character is a space
				//increment $i
				$i = ++$i;
			break;

			default: //Character is something else
				//Get the interface name and add it to the $interfaces array
				while (ord(substr($output, $n, 1)) != 32):
					//Add current character to tempStr
					$tmpStr=$tmpStr . substr($output, $n, 1);
					//increment $n
					$n= ++$n;
				endwhile;
			//Add the interface to the $allInterfaces array
			$allInterfaces[]=$tmpStr;
			//increment $i
			$i = ++$i;
			break;
		}
	endwhile;

	//Check $allInterfaces against bridge names, add them to $interfaces only if
	//they don't match any of the bridge names.

	//Loop through interfaces
	for ($i = 0; $i < count($allInterfaces); $i++) {
		$validInt=FALSE;
		//Loop through bridges if there are any
		if (! empty($bridgeName)) {
			for ($n = 0; $n < count($bridgeName); $n++) {
				if ($allInterfaces[$i] != $bridgeName[$i]) {
					$validInt=TRUE;
				}
			}
		} else {
			//No bridges so interface is valid
			$validInt=TRUE;
		}
		//Add interface to $interfaces if it is valid
		if ($validInt==TRUE) {
			$interfaces[]=$allInterfaces[$i];
		}
	}
}
?>
