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

$KERNEL_VER    =    "2.6"; //Must be changed to "2.4" or "2.6" depending on your kernel version.
$brctl_CMD     =    "/sbin/brctl "; //you only need to change this if you have bridge-utils.
									//installed.  Must be the correct path for the brctl command.
$tc_CMD        =    "sudo /sbin/tc "; //Must be set to "sudo" then the correct path for the tc command.
$ifconfig_CMD  =    "/sbin/ifconfig "; //Must be set to the correct path of the ifconfig command
$onOffFile      =   "/tmp/netemstate.txt"; //Must be a name of a file in a writeable directory (tmp is usually
					  //the best location) You can call this file whatever you want as long
					  //as it's not the same as any other file in the same path.
$disconnect_DIR = "/root/nambiar/disc_new_port_int";
$wanchar_DIR = "sudo /root/hemanta";
?>
