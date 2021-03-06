<?php
/*
 * Name: CD2_Abfrage.php
 * 
 * Description: UI for the Change Detector 
 * 
 * Author: Anselm Metzger
 * 
 * includes: cd2db_query.inc : database query 
 * 			 Languagecodes.inc : translating Languagecodes
 *
 * 
 Copyright (c) 2012, Wikimedia Deutschland (Anselm Metzger)
  All rights reserved.
 
  Redistribution and use in source and binary forms, with or without
  modification, are permitted provided that the following conditions are met:
      * Redistributions of source code must retain the above copyright
        notice, this list of conditions and the following disclaimer.
      * Redistributions in binary form must reproduce the above copyright
        notice, this list of conditions and the following disclaimer in the
        documentation and/or other materials provided with the distribution.
      * Neither the name of Wikimedia Deutschland nor the
        names of its contributors may be used to endorse or promote products
        derived from this software without specific prior written permission.
 
  THIS SOFTWARE IS PROVIDED BY WIKIMEDIA DEUTSCHLAND ''AS IS'' AND ANY
  EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
  WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
  DISCLAIMED. IN NO EVENT SHALL WIKIMEDIA DEUTSCHLAND BE LIABLE FOR ANY
  DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
  (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
  LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
  ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
  (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
  SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 
  NOTE: This software is not released as a product. It was written primarily for
 Wikimedia Deutschland's own use, and is made public as is, in the hope it may
 be useful. Wikimedia Deutschland may at any time discontinue developing or
 supporting this software. There is no guarantee any new versions or even fixes
 for security issues will be released.
*/

$start = time();
include("inc/src/cd2db_query.inc");
include("inc/src/Languagecodes.inc");
?>
<script type="text/javascript" src="/<?php echo $tsAccount; ?>/toolkit/js/jquery.fixedtable.js"></script>
<script type="text/javascript">
	$( document ).ready( function() {
		drawTable();
	} );

	function drawTable() {
		$(".resultDiv").each( function() {
			var Id = $( this ) . get( 0 ) . id;
			var maintbheight = 360;
			var maintbwidth = 1000; /* another fix for the fix. why would anyone use the *window* width here in the first place?! */
			
			$( "#" + Id + " .resultTable" ).fixedTable( {
				width: maintbwidth,
				height: maintbheight,
				fixedColumns: 1,
				classHeader: "fixedHead",
				classFooter: "fixedFoot",
				classColumn: "fixedColumn",
				fixedColumnWidth: 250,
				outerId: Id,
				backcolor: "blue"
			} );
		} );
	}

	function openAdvancedSettings () {
		if( document.getElementById( "AdvancedSearch" ).style.display == "block" ) {
			document.getElementById( "AdvancedSearch" ).style.display = "none";
		} else {
			document.getElementById( "AdvancedSearch" ).style.display = "block";
		}
	}
</script>

<?php
// Defining the Entryform
setlocale(LC_TIME, 'de_DE');
$timestamp_since = '1330849800'; // 4.3.2012 8:30 UTC
$today = time();
$timestamp_since = $today - ( 7 * 24 * 60 * 60 );
$yesterday = $today - ( 1 * 24 * 60 * 60 );
// check for newest data
$check_yesterday_file = "src/tmp/tmp_" . date( 'Ymd', $yesterday ) . ".ok.dump";
if( !file_exists( $check_yesterday_file ) ) {
	$yesterday = $yesterday - ( 1 * 24 * 60 * 60 );
}
?>

<div id="Ueberschrift" style="float: left; vertical-align: middle;">
	<div id="Introduction">
		<h2><?php echo $Headline; ?></h2>
		<p id="Description"><?php echo $Description; ?></p>
		<h2 onclick="toggleDescription()" style="cursor: pointer;"><?php echo $Headline2; ?><img id="expandIcon" src="../img/expand-large-silver.png" style="width: 15px; height: 15px; padding-left: 10px;"></h2>
		<p id="Description2" class="displayNone"><?php echo $Description2; ?></p>
	</div>

	<form action="<?php echo $_SERVER['PHP_SELF']; ?>#result_table" method="get">
		<p id="Description"><?php echo $errorMessage; ?></p>
		<p id="Description"><?php echo $Formtext; ?></p>
		<div id="Eingabe">
			<div id="AdvancedSearch">
				<a onclick="javascript:openAdvancedSettings()">
					<?php echo $Settings["Headline"] ?>
				</a>
				<ul>
					<li title="<?php echo $Settings["HalfTooltip"] ?>">
						<?php echo $Settings["Half"] ?>: 
						<input type="checkbox" name="Cuthalf"<?php if (isset($_GET["Cuthalf"]) OR !isset($_GET["submit"])) {echo "checked=\"checked\"";} ?>>
					</li>
					<li title="<?php echo $Settings["SortingTooltip"] ?>">
						<?php echo $Settings["Sorting"] ?>: 
						<?php echo $Settings["SortingNoChange"] ?>
						<input type="radio" name="Sorting" value="No_change" checked="checked">
						<?php echo $Settings["SortingNews"] ?>
						<input type="radio" name="Sorting" value="News" <?php if ($_GET["Sorting"] == "News") {echo "checked=\"checked\"";} ?>>
					</li>
					<li>
						<span >Filter:</span>
						<ul>
							<li>
								<input name="filterMU" type="checkbox" <?php if (isset($_GET["filterMU"]) OR !isset($_GET["submit"])) {echo "checked=\"checked\"";} ?>> 
								<span title="<?php echo $Filter["m_uTooltip"]; ?>">
									<?php echo $Filter["m_u"]; ?>
								</span>
							</li>
							<li>
								<input name="filterNB" type="checkbox" <?php if (isset($_GET["filterNB"]) OR !isset($_GET["submit"])) {echo "checked=\"checked\"";} ?>> 
								<span title="<?php echo $Filter["n_bTooltip"]; ?>">
									<?php echo $Filter["n_b"]; ?>
								</span>
							</li>
							<li>
								<input name="filterOM" type="checkbox" <?php if (isset($_GET["filterOM"]) OR !isset($_GET["submit"])) {echo "checked=\"checked\"";} ?>> 
								<span title="<?php echo $Filter["o_mTooltip"]; ?>">
									<?php echo $Filter["o_m"]; ?>
								</span>
							</li>
						</ul>
					</li>
				</ul>
			</div>

			<div style="float:left;">
				<p>
					<select name="day" size="1">
						<?php
						$temp_time = $yesterday;
						for( ; ; ) {
							echo "<option value=\"" . date( 'Ymd',$temp_time ) . "\"";
							if( $_GET["day"] == date( 'Ymd', $temp_time ) ) {
								echo "selected='selected'";
							}
							echo ">" . date( 'd-m-Y', $temp_time ) . "</option>";	
							$temp_time = $temp_time - ( 1 * 24 * 60 * 60 );
							if( $temp_time < $timestamp_since ) {
								break;
							}
						}
						?>
					</select>
				</p>
				<p title="<?php echo $Settings["LanggroupTooltip"]; ?>">
					<?php echo $Settings["Langgroup"]; ?>: 
					<input type="radio" name="Langgroup" value="EU" checked="checked" />
					<?php echo $Settings["EU"]; ?>
					<input type="radio" name="Langgroup" value="All" <?php if ($_GET["Langgroup"] == "All") {echo "checked=\"checked\"";} ?> />
					<?php echo $Settings["World"] ?>
				</p>
				<p title="<?php echo $Settings["ReferenzlangTooltip"]; ?>">
					<?php echo $Settings["Referenzlang"] ?>: 
					<input name="Reflang" size="3" value="<?php if (isset($_GET["Reflang"])) {echo $_GET["Reflang"];} else {echo $lang;} ?>" />
				</p>
				<p>
					<a id="AdvancedSetting" onclick="javascript:openAdvancedSettings()">
						<?php echo $Settings["Headline"] ?>
					</a>
				</p>
				<p>
					<input name="submit" type="submit" value="<?php echo $Formbutton; ?>" />
				</p>
			</div>
		</div>
	</form>
</div>

<div style="width: 98%; padding: 1em; clear:both;"></div>

<?php
// Evaluating the parameter of the form
if( isset( $_GET["submit"] ) ) {
	if( $_GET["Reflang"] != "" ) {
		$reflang = $_GET["Reflang"];
	}

	$Date = $_GET["day"];
	$SortingOption = 0;
	if( $_GET["Sorting"] == "News" ) {
		$SortingOption = 1;
	}
			
	if( $_GET["Langgroup"] == "EU" ) {
		$LangGroup = array( "de","en", "fr", "pt","it","pl","ru","nl","sv","es" );
	}
	
	if( $_GET["Langgroup"] == "All" ) {
		$LangGroup = array( "de","en","fr", "pt","it","pl","ru","nl", "sv","es", "ja","zh" );
	}

	$Cuthalf = FALSE;
	if( $_GET["Cuthalf"] == "on" ) {
		$Cuthalf = TRUE;
	}

	$No_Filter["m_u"] = TRUE;
	if( $_GET["filterMU"] != "on" ) {
		$No_Filter["m_u"] = FALSE;
	}

	$No_Filter["n_b"] = TRUE;
	if( $_GET["filterNB"] != "on" ) {
		$No_Filter["n_b"] = FALSE;
	}

	$No_Filter["o_m"] = TRUE;
	if( $_GET["filterOM"] != "on" ) {
		$No_Filter["o_m"] = FALSE;
	}

	$flipped_Langgroup = array_flip( $LangGroup );
	if( !array_key_exists( $reflang, $flipped_Langgroup ) ) {
		echo '<div id="Errormessage"><span>';
		printf( $Error["NotinGrp"], $reflang, $_GET["Langgroup"] );
		echo "</span></div>";
		break;
	}

	// Put the Reflang on first position of the Langgroup
	array_splice($LangGroup,$flipped_Langgroup[$reflang],1);
	array_unshift($LangGroup, $reflang);

	// Database-query or load from file
	// Unique filename 
	$file_name = "tmp_" . $Date . $_GET["Langgroup"] . "1" . $_GET["Cuthalf"] . "2" .
			$_GET["filterMU"] . "3" . $_GET["filterNB"] . "4" . $_GET["filterOM"] . ".dump";

	if( file_exists( "src/tmp/" . $file_name ) && filesize( "src/tmp/" . $file_name ) > 8 ) {
		$db_result = unserialize( file_get_contents( "src/tmp/" . $file_name ) );
	} else {
		$db_result = query_change_db( $Date, $LangGroup, $Cuthalf, $No_Filter, 'p50380g50613__change_detector' );
		$uniqueID = uniqid( "tmp" ) . ".tmp";
		file_put_contents( "src/tmp/" . $uniqueID, serialize( $db_result ) );
		rename( "src/tmp/" . $uniqueID, "src/tmp/" . $file_name );
	}

	$LangGroup = $db_result['real_LangGroup'];

	// Enriching the database result
	foreach( $db_result as $id => $db_Entry ) {
		if( $id == "real_LangGroup" ) {
			continue;
		}
		$result_Entry = $db_Entry;
		$result_Entry["ChangedSum"] = count( $db_Entry["Changed"] );
		if ( array_key_exists( "Unchanged", $db_Entry ) ) {
			$result_Entry["UnchangedSum"] = count( $db_Entry["Unchanged"] );
		} else {
			$result_Entry["UnchangedSum"] = 0;
		}
		$result_Entry["Reflang"] = $reflang;

		if( array_key_exists( $reflang, $db_Entry["Changed"] ) ) {
			$result_Entry["Refchanged"] = "-1";
		}

		$result_Entry["titlelang"] = $reflang;
		if( array_key_exists( $reflang, $db_Entry["Changed"] ) ) {
			$result_Entry["article"] = $db_Entry[$reflang]["title"];
			$result_Entry["Refchanged"] = "1";
		} else if( array_key_exists( "Unchanged", $db_Entry ) && array_key_exists( $reflang, $db_Entry["Unchanged"] ) ) {
			$result_Entry["article"] = $db_Entry[$reflang]["title"];
			$result_Entry["Refchanged"] = "-1";
		} else {
			$otherTitle = search_other_article( $db_Entry, $reflang );
			$result_Entry["article"] = $otherTitle["article"];
			$result_Entry["titlelang"] = $otherTitle["lang"];
			$result_Entry["Refchanged"] = "0";
		}

		if( count( array_intersect_key( $result_Entry["Changed"], array_flip( $LangGroup ) ) ) < 3 ) {
			continue;
		}
		$Final_Result[] = $result_Entry;
	}

	// Sorting 
	foreach( $Final_Result as $key => $row ) {
		$Change_of_reflang[$key] = $row["Refchanged"];
		$ChangedSum[$key] = $row["ChangedSum"];
		$Name[$key] = $row["article"];
	}

	if ( $SortingOption == 1 ) {
		array_multisort( $ChangedSum, SORT_DESC, $Change_of_reflang, SORT_DESC, $Name, SORT_ASC, $Final_Result );
	} else {
		array_multisort( $Change_of_reflang, SORT_ASC, $ChangedSum, SORT_DESC, $Name, SORT_ASC, $Final_Result );
	}

	// Adding Entrys for 'no article'
	foreach( $Final_Result as $k => $Entry ) {
		foreach( $LangGroup as $key => $Language ) {
			if( !array_key_exists( $Language, $Entry ) ) {
				$Entry["missing"][] = $Language;
				$Entry[$Language]["title"] = "x_blank_x";
			}
		}
	}

	// Contruction of the result table
	$count = sizeof( $LangGroup );
?>

<a name="result_table"></a>
<div id="Ergebnis" class="resultDiv">
	<table id="tableResult" class="resultTable">
		<thead>
			<tr>
				<th>
					<span><?php echo $Articlename; ?></span>
				</th>
				<?php foreach( $LangGroup as $key => $Language ): ?>
				<th>
					<span title="<?php echo langcode_in_en($Language); ?>">
						<?php echo langcode_in_local( $Language ); ?>
					</span>
				</th>
					<?php $count --; ?>
				<?php endforeach; ?>
			</tr>
		</thead>
		<tbody>
			<?php foreach( $Final_Result as $k => $v ): ?>
			<tr>
				<td style="text-align: right;">
					<a href="http://<?php echo $v["titlelang"]; ?>.wikipedia.org/wiki/<?php echo $v["article"]; ?>" target="blank"><?php echo str_replace( "_", " ", $v["article"] ); ?></a>
				</td>
				<?php 
				foreach( $LangGroup as $key => $Language ) {
					$result_cell_color = "white";
					$result_cell_fontcolor = "#ccc";
					$result_cell_text = "no article";
					$existent = false;
					$result_cell_class = "";
		
					if( array_key_exists( $Language, $v["Changed"] ) ) {
						$result_cell_color = "#008000";
						$result_cell_fontcolor = "white";
						$result_cell_text = "changed";
						$existent = true;
					}

					if( array_key_exists( "Unchanged", $v ) && array_key_exists( $Language, $v["Unchanged"] ) ) {
						$result_cell_class = "unch";
						$result_cell_color = "white";
						$result_cell_fontcolor = "red";
						$result_cell_text = "no change";
						$existent = true;
					}
					?>
					<td class="<?php echo $result_cell_class; ?>" style="background: <?php echo $result_cell_color; ?>; text-align: center;">
					<?php if ($existent) { ?>
						<a style="text-decoration: none; color: <?php echo $result_cell_fontcolor; ?>" href="http://<?php echo $Language; ?>.wikipedia.org/wiki/<?php echo $v[$Language]["title"]; ?>" target="_blank">
							<span title="<?php echo str_replace( "_", " ", $v[$Language]["title"] ); ?>">
								<?php echo $result_cell_text; ?>
							</span>
						</a>
					<?php } else { ?>
						<span style="text-decoration: none;  color: <?php echo $result_cell_fontcolor; ?>">
							<span>no article</span>
						</span>
					<?php }	 ?>
					</td>
				<?php
				}
				?>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>
<div style="width: 98%; padding: 1em; clear:both;"></div>

<?php if( !array_key_exists( $reflang, array_flip( $LangGroup ) ) ): ?>
	<div id="Errormessage">
		<span><?php printf( $Error["NotinDay"], $reflang ); ?></span>
	</div>
<?php endif; ?>

<?
}
$end = time();

if( isset( $_GET["submit"] ) ) {
	// log request
	$asqmId = ( isset( $_SESSION['asqmId'] ) && !empty( $_SESSION['asqmId'] ) ) ? $_SESSION['asqmId'] : "";

	$userInfo = posix_getpwuid( posix_getuid() );
	$dbCred = parse_ini_file( $userInfo['dir'] . "/replica.my.cnf" );
	mysql_connect( 'tools-db', $dbCred['user'], $dbCred['password'] );
	mysql_select_db( 'p50380g50454__request_logs' );

	$serializedResult = base64_encode( serialize( $arrResult ) );
	$sql = "INSERT INTO request_log ".
			"(asqm_id, title, lang, action_type, result, request_time) ".
			"VALUES ('" . $asqmId . "', '', '" . $reflang . "', 'cd-usage', '', NOW())";
	mysql_query( $sql );
	$mysqlError = mysql_error();
	echo $mysqlError;
	mysql_close();
}
