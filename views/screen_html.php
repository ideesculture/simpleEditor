<?php
/* ----------------------------------------------------------------------
 * views/editor/objects/screen_html.php : 
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2008-2013 Whirl-i-Gig
 *
 * For more information visit http://www.CollectiveAccess.org
 *
 * This program is free software; you may redistribute it and/or modify it under
 * the terms of the provided license as published by Whirl-i-Gig
 *
 * CollectiveAccess is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTIES whatsoever, including any implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
 *
 * This source code is free and modifiable under the terms of 
 * GNU General Public License. (http://www.gnu.org/copyleft/gpl.html). See
 * the "license.txt" file for details, or visit the CollectiveAccess web site at
 * http://www.CollectiveAccess.org
 *
 * ----------------------------------------------------------------------
 */
	require_once(__CA_APP_DIR__."/plugins/simpleEditor/helpers/displayHelpers.php");
 	$t_object 			= $this->getVar('t_subject');
	$vs_subject_table    = get_class($t_object);
	$vn_type_id         = $this->getVar('type_id');
	$vn_above_id 		= $this->getVar('above_id');

	$vb_can_edit	 	= $t_object->isSaveable($this->request);
	$vb_can_delete		= $t_object->isDeletable($this->request);

	$vs_rel_table		= $this->getVar('rel_table');
	$vn_rel_type_id		= $this->getVar('rel_type_id');
	$vn_rel_id			= $this->getVar('rel_id');


	$vs_last_selected_path_item = str_ireplace("edit/","",$this->getVar('last_selected_path_item'));
	$vs_default_screen 	= $this->getVar('default_screen');

	$va_screens = $this->getVar("screens");

	// Getting first screen of non default ones
	$vs_screen_code = key($va_screens);
	$vs_first_non_default_screen = str_replace("screen_","Screen",$vs_screen_code);

	switch($vs_subject_table) {
		case "ca_occurrences":
			$vs_classic_editor    = "/editor/occurrences/OccurrenceEditor";
			$vs_id_var          = "occurrence_id";
			$vs_simpleEditor_controller = "Occurrences";
			break;
		case "ca_storage_locations":
			$vs_classic_editor    = "/editor/storage_locations/StorageLocationEditor";
			$vs_id_var          = "location_id";
			$vs_simpleEditor_controller = "StorageLocations";
			break;
		case "ca_collections":
			$vs_classic_editor    = "/editor/collections/CollectionEditor";
			$vs_id_var          = "collection_id";
			$vs_simpleEditor_controller = "Collections";
			break;
		case "ca_places":
			$vs_classic_editor    = "/editor/places/PlaceEditor";
			$vs_id_var          = "place_id";
			$vs_simpleEditor_controller = "Places";
			break;
		case "ca_entities":
			$vs_classic_editor    = "/editor/entities/EntityEditor";
			$vs_id_var          = "entity_id";
			$vs_simpleEditor_controller = "Entities";
			break;
		case "ca_loans":
			$vs_classic_editor    = "/editor/loans/LoanEditor";
			$vs_id_var          = "loan_id";
			$vs_simpleEditor_controller = "Loans";
			break;
		case "ca_objects":
		default:
			$vs_classic_editor    = "/editor/objects/ObjectEditor";
			$vs_id_var          = "object_id";
			$vs_simpleEditor_controller = "Objects";
			break;
	}

	$vn_subject_id = ($this->getVar('subject_id') ? $this->getVar('subject_id') : $this->getVar($vs_id_var));
?>
	<div id="topNavSecondLine"><div id="toolIcons">
			<script type="text/javascript">
				function caToggleItemWatch() {
					var url = '<?php print __CA_URL_ROOT__; ?>/index.php<?php print $vs_classic_editor."/toggleWatch/".$vs_id_var."/".$vn_subject_id ?>';
					//console.log(url);
					jQuery.getJSON(url, {}, function(data, status) {
						if (data['status'] == 'ok') {
							jQuery('#caWatchItemButton').html(
								(data['state'] == 'watched') ?
									'Ne plus surveiller' :
									'Surveiller'
							);
						} else {
							console.log('Error toggling watch status for item: ' + data['errors']);
						}
					});
				}
			</script>

		</div>
		
	</div>
	<div id="simple_editor_top">
		<div id="top_box">

				<?php //if($vn_subject_id) {
				if (($vs_priv_table_name = $vs_table_name) == 'ca_list_items') {
					$vs_priv_table_name = 'ca_lists';
				}

				$va_reps = $t_object->getRepresentations(array('preview170','medium'));
				//var_dump($va_reps);
				//die();
				$vs_buf = "";
				$va_imgs = array();
				if ((sizeof($va_reps) > 0) &&($vs_subject_table == "ca_objects")) {

					$vn_r = $vn_primary_index = 0;
					foreach ($va_reps as $key=>$va_rep) {
						if (!($va_rep['info']['preview170']['WIDTH'] && $va_rep['info']['preview170']['HEIGHT'])) {
							continue;
						}

						if ($vb_is_primary = (isset($va_rep['is_primary']) && (bool)$va_rep['is_primary'])) {
							$vn_primary_index = $vn_r;
							$va_img_primary = $va_rep['urls']['medium'];
							$va_img_primary_representation_id = $va_rep["representation_id"];
							//die();
						}
						$va_imgs[$va_rep["representation_id"]] = $va_rep['urls']['medium'];
						$vn_r++;
					}

					if($va_img_primary) {
						$vs_buf .= "<div id=\"simple_editor_main_img\" class=\"simple_editor_img_primary\" style=\"background-image:url($va_img_primary);background-size:contain;background-position:50% 50%;background-repeat:no-repeat;\" data-object-id=\"".$vn_subject_id."\" data-representation-id=\"".$va_img_primary_representation_id."\" onclick='caMediaPanel.showPanel(\"".__CA_URL_ROOT__."/index.php/editor/objects/ObjectEditor/GetRepresentationEditor/object_id/".$vn_subject_id."/representation_id/\"+jQuery(this).data(\"representationId\")); return false;' ><span class=\"helper\"></span></div>";
						//$vs_buf .= "<div><a class='qlButton' onclick='caMediaPanel.showPanel(\"/index.php/find/SearchObjects/QuickLook/object_id/3476\"); return false;' >Quick Look</a></div>";
// index.php/editor/objects/ObjectEditor/GetRepresentationEditor/object_id/".$vn_subject_id."/representation_id/".$va_img_primary_representation_id

					}
					if (sizeof($va_imgs)) {
						foreach($va_imgs as $key=>$va_img) {
							$vs_imgs_buf .= "<span class='image-cell'><img src=".$va_img." data-representation-id=\"".$key."\"></span>";
						}
						$vs_buf .= "<div class='simple_editor_imgs_wrapper1'><div class='simple_editor_imgs_wrapper2'><div class='simple_editor_imgs'>".$vs_imgs_buf."</div></div></div>";
					}

					$vs_buf .= "
					    <script type='text/javascript'>
					        jQuery(document).ready(function() {
					            jQuery('.simple_editor_imgs IMG').on('click', function(){
					                var clicked_image_src = jQuery(this).attr('src');
					                var clicked_image_representation_id = jQuery(this).data('representationId');
					                var main_image = jQuery('#simple_editor_main_img');
					                main_image.css('background-image', 'url('+clicked_image_src+')');
					                main_image.data('representationId', clicked_image_representation_id);
					                main_image.attr('data-representation-id', clicked_image_representation_id);
					            });
					        });
					    </script>
					    ";
					print "<div id=\"medias_box\" style='width:324px;'>".$vs_buf."</div>";

				}

				//}
				?>

			<div id="top_editor_box" style="<?php 
				if ((sizeof($va_reps) > 0) &&($vs_subject_table == "ca_objects")) {
					print 	"width:65%";
				} else {
					print "width:100%";
				}
			?>">
				<div class="control-box rounded" id="topButtons">
					<a href="#" class="form-button 1457282139" onclick='jQuery("#topform").submit();' id="<?php print $vs_form_id; ?>_submit"><span class="form-button">Enregistrer</span></a>
					<?php if ($vn_subject_id): ?>
						<a href="<?php print __CA_URL_ROOT__; ?>/index.php/simpleEditor/<?php print $vs_simpleEditor_controller; ?>/Edit/<?php print $vs_last_selected_path_item; ?>"
						   class="form-button"><span class="form-button ">Annuler</span></a>
						<div class="watchThis"><a href="#" title="Surveiller cet enregistrement"
												  onclick="caToggleItemWatch(); return false;" id="caWatchItemButton">Surveiller</a>
						</div>
						<div id="caDuplicateItemButton" title="Duplique cet objet">
							<form action="<?php print __CA_URL_ROOT__; ?>/index.php/simpleEditor/<?php print $vs_simpleEditor_controller; ?>/Edit"
								  method="post" id="DuplicateItemForm" target="_top" enctype="multipart/form-data">
								<input type="hidden" name="_formName" value="DuplicateItemForm">
								<a href="#" onclick="document.getElementById(&quot;DuplicateItemForm&quot;).submit();"
								   class="">Dupliquer</a><input name="object_id"
																									   value="<?php print $vn_subject_id; ?>"
																									   type="hidden">
								<input name="mode" value="dupe" type="hidden">
							</form>
						</div>
							<a class="form-button 1457282139" href="<?php print __CA_URL_ROOT__; ?>/index.php<?php print $vs_classic_editor; ?>/PrintSummary/<?php print $vs_id_var."/".$vn_subject_id; ?>">
								<span class="form-button">
									PDF
								</span>
							</a>
						<a
							href="<?php print __CA_URL_ROOT__; ?>/index.php/simpleEditor/<?php print $vs_simpleEditor_controller; ?>/Delete/<?php print ($vn_type_id ? "type_id/".$vn_type_id."/" : ""); ?><?php print $vs_id_var."/".$vn_subject_id; ?>"
							class="form-button deleteButton"><span class="form-button ">Supprimer</span></a>
						<?php endif; ?>
				</div>

				<?php
				// Saving with default editor
				//print caFormTag($this->request, 'Save/'.$vs_default_screen.'/'.$vs_id_var.'/'.$vn_subject_id.($vn_type_id ? "/type_id/".$vn_type_id : ""), 'topform', "simpleEditor/".$vs_simpleEditor_controller, 'POST', 'multipart/form-data');

				// Saving with simpleEditor
				//print caFormTag($this->request, 'Save/'.$vs_default_screen.'/object_id/'.$vn_object_id, 'ObjectEditorTopForm', "simpleEditor/Objects", 'POST', 'multipart/form-data');

				// Saving with simpleEditor
				//print caFormTag($this->request, 'Save/'.$vs_default_screen.'/object_id/'.$vn_object_id, 'ObjectEditorTopForm', "simpleEditor/Objects", 'POST', 'multipart/form-data');

				?>
				<div class="bundles" id="top_form">

				</div>
				<!--</form>-->

			</div>
		</div>
	</div>

	<div id="screensList">
<?php
	foreach($va_screens as $va_screen) {
		// We display only the non-default screens, as the default one is on top left of the screen
		if (($va_screen["default"]["action"] == $vs_last_selected_path_item) || ($va_screen["default"]["action"] == "edit/".$vs_last_selected_path_item )){
			// Current loaded screen
			$vs_class="screen_button current";
		} else {
			$vs_class="screen_button";
		}
		//print caNavLink($this->request, $va_screen["displayName"], $vs_class, "*", "*", $va_screen["default"]["action"],array("object_id"=>$vn_object_id) );
		$vs_screen_name = str_ireplace("edit/","", $va_screen["default"]["action"]);
		$vs_screen_name = str_ireplace("save/","", $vs_screen_name);
		print "<a onclick=\"toggleSimpleEditorLowerForm('".$vs_simpleEditor_controller."Ajax"."','".$vs_screen_name."','".$vs_id_var."/".$vn_subject_id."','bottomform');\" class=\"".$vs_class." ".$vs_screen_name."\">".$va_screen["displayName"]."</a>";
	}
?>
	</div>
	<div id="lower_form">
	</div>
	<div class="editorBottomPadding"><!-- empty --></div>
	
<?php
	print caSetupEditorScreenOverlays($this->request, $t_object, $va_bundle_list);
	//Temporary disabling extraction of current screen
	//$this_screen = ($vs_last_selected_path_item ?  $vs_last_selected_path_item : $vs_first_non_default_screen);
	if($vs_last_selected_path_item != $vs_default_screen) {
		$this_screen = $vs_last_selected_path_item;
	} else {
		$this_screen = $vs_first_non_default_screen;
	}
	print "<script>console.log('".$this_screen."');</script>";

?>

<script type="text/javascript">
	function toggleSimpleEditorLowerForm(ajaxController,screenname,id,form) {
		if(screenname) {
			var url = "<?php print __CA_URL_ROOT__; ?>/index.php/simpleEditor/"+ajaxController+"/editAjax/"+screenname+"/"+id+"<?php print "/type_id/".$vn_type_id; ?>/form/"+form;
			console.log("Refreshing lower_form with "+url);
			jQuery.ajax({
				url: url,
				cache: false
			}).done(function( html ) {
				jQuery( "#lower_form" ).html(html);
				/*jQuery( "#lower_form" ).css("position","fixed");*/
				jQuery( "#lower_form" ).fadeIn();

				// Hiding (-) relations
                jQuery(".relationship_typename").each(function() {
                    if(jQuery(this).text() == "(-)") {
                        jQuery(this).text("");
                    }
                });
			});
		} else {
			jQuery("#lower_form").hide();
			jQuery("#screensList").hide();
		}
		window.setTimeout(function() {
			jQuery(".labelInfo").show();
		}, 800);
	}

	function loadSimpleEditorTopForm(ajaxController,screenname,id,form) {
	    var url = "<?php print __CA_URL_ROOT__; ?>/index.php/simpleEditor/"+ajaxController+"/editAjax/"+screenname+"/"+id+"<?php print "/type_id/".$vn_type_id; ?>/form/"+form;
	    console.log("Refreshing top_form with "+url);
	    jQuery.ajax({
	        url: url,
	        cache: false
	    }).done(function( html ) {
	        jQuery( "#top_form" ).html(html);
	        jQuery( "#top_form" ).fadeIn();

			// Hiding (-) relations
            jQuery(".relationship_typename").each(function() {
                if(jQuery(this).text() == "(-)") {
                    jQuery(this).text("");
                }
            });

	    });
		window.setTimeout(function() {
			jQuery(".labelInfo").show();
		}, 800);
	}

	jQuery(document).ready(function(){
		// Hiding after delay simple notifications (for example Saved)
		jQuery(".notification-info-box").delay(1000).slideUp();
		
		loadSimpleEditorTopForm('<?php print $vs_simpleEditor_controller."Ajax"; ?>', '<?php print $vs_default_screen; ?>','<?php print "/type_id/".$vn_type_id."/".$vs_id_var."/".$vn_subject_id; ?>','topform');
		jQuery('#screensList').find("A").removeClass('current');
		<?php if($this_screen) : ?>
		jQuery('#screensList').find("A.<?php print $this_screen; ?>").addClass('current');
		<?php endif; ?>
		window.setTimeout(toggleSimpleEditorLowerForm, 100,'<?php print $vs_simpleEditor_controller."Ajax"; ?>', '<?php print $this_screen; ?>','<?php print "/type_id/".$vn_type_id."/".$vs_id_var."/".$vn_subject_id; ?>','bottomform');

	    jQuery("#top_editor_box .bundles").fadeIn();

	    jQuery("#screensList a").on("click",function(){
	        jQuery("#screensList").find("A").removeClass('current');
	        jQuery(this).addClass('current');
	        jQuery( "#lower_form" ).hide();
	    });

		caUI.utils.disableUnsavedChangesWarning();
	});
</script>
