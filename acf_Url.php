<?php

/*
 *	Advanced Custom Fields - URL fields
 *
 *  Register the field with register_field( 'acf_Url', $file_path );
 *
 *  @author Jamie Schembri <jamie@schembri.me>
 *
 */


class acf_Url extends acf_Field
{

	/*--------------------------------------------------------------------------------------
	*
	*	Constructor
	*	- This function is called when the field class is initalized on each page.
	*	- Here you can add filters / actions and setup any other functionality for your field
	*
	*	@author Elliot Condon
	*	@since 2.2.0
	*
	*-------------------------------------------------------------------------------------*/

	function __construct($parent)
	{
		// do not delete!
  	parent::__construct($parent);

  	// set name / title
  	$this->name = 'url'; // variable name (no spaces / special characters / etc)
		$this->title = __( 'URL', 'acf' ); // field label (Displayed in edit screens)

   	}


	/*--------------------------------------------------------------------------------------
	*
	*	create_options
	*	- this function is called from core/field_meta_box.php to create extra options
	*	for your field
	*
	*	@params
	*	- $key (int) - the $_POST obejct key required to save the options to the field
	*	- $field (array) - the field object
	*
	*	@author Elliot Condon
	*	@since 2.2.0
	*
	*-------------------------------------------------------------------------------------*/

	function create_options($key, $field)
	{
		$field['allow_internal'] = isset( $field['allow_internal'] ) ? $field['allow_internal'] : 'no';

		?>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e( 'Allow Internal', 'acf' ); ?></label>
			</td>
			<td>
				<?php
					$this->parent->create_field(array(
						'type'    => 'radio',
						'name'    => 'fields[' . $key . '][allow_internal]',
						'value'   => $field['allow_internal'],
						'layout'  => 'horizontal',
						'choices' => array(
							'yes'   => __( 'Yes', 'acf' ),
							'no'    => __( 'No', 'acf' ),
						)
					));
				?>
			</td>
		</tr>
		<?php
	}


	/*--------------------------------------------------------------------------------------
	*
	*	pre_save_field
	*	- this function is called when saving your acf object. Here you can manipulate the
	*	field object and it's options before it gets saved to the database.
	*
	*	@author Elliot Condon
	*	@since 2.2.0
	*
	*-------------------------------------------------------------------------------------*/

	function pre_save_field($field)
	{
		// do stuff with field (mostly format options data)

		return parent::pre_save_field($field);
	}


	/*--------------------------------------------------------------------------------------
	*
	*	create_field
	*	- this function is called on edit screens to produce the html for this field
	*
	*	@author Elliot Condon
	*	@since 2.2.0
	*
	*-------------------------------------------------------------------------------------*/

	function create_field($field)
	{
		$input_type = 'url';

		// are we allowing internal links?
		if ( $field['allow_internal'] === 'yes' ) {
			// then we have to revert to using a text field instead of a url field.
			// compatible browsers will not accept an internal URL otherwise, and we
			// can't apply novalidate to the form at this stage.
			$input_type = 'text';
		}

		echo '<input type="', $input_type, '" value="' . $field['value'] . '" id="' . $field['name'] . '" class="' . $field['class'] . '" name="' . $field['name'] . '" />';
	}


	/*--------------------------------------------------------------------------------------
	*
	*	admin_head
	*	- this function is called in the admin_head of the edit screen where your field
	*	is created. Use this function to create css and javascript to assist your
	*	create_field() function.
	*
	*	@author Elliot Condon
	*	@since 2.2.0
	*
	*-------------------------------------------------------------------------------------*/

	function admin_head()
	{
		?>
		<style>
			.acf_postbox .field input[type="url"] {
				width: 100%;
				padding: 5px;
				resize: none;
			}
		</style>
		<?php
	}


	/*--------------------------------------------------------------------------------------
	*
	*	admin_print_scripts / admin_print_styles
	*	- this function is called in the admin_print_scripts / admin_print_styles where
	*	your field is created. Use this function to register css and javascript to assist
	*	your create_field() function.
	*
	*	@author Elliot Condon
	*	@since 3.0.0
	*
	*-------------------------------------------------------------------------------------*/

	function admin_print_scripts()
	{

	}

	function admin_print_styles()
	{

	}


	/*--------------------------------------------------------------------------------------
	*
	*	update_value
	*	- this function is called when saving a post object that your field is assigned to.
	*	the function will pass through the 3 parameters for you to use.
	*
	*	@params
	*	- $post_id (int) - usefull if you need to save extra data or manipulate the current
	*	post object
	*	- $field (array) - usefull if you need to manipulate the $value based on a field option
	*	- $value (mixed) - the new value of your field.
	*
	*	@author Elliot Condon
	*	@since 2.2.0
	*
	*-------------------------------------------------------------------------------------*/

	function update_value($post_id, $field, $value)
	{
		// do stuff with value

		// sanitize
		$value = filter_var( $value, FILTER_SANITIZE_URL );

		// are we allowing internal URLs?
		if (  $field['allow_internal'] === 'no' ) {
			// no, so check for http:// at start
			if ( ! (preg_match( '#^(http|https)://#', $value ) ) ) {
				// missing http://, add it (assume insecure)
				$value = 'http://' . $value;

				// finally, check if it's a valid URL
				if ( ! filter_var( $value, FILTER_VALIDATE_URL ) ) {
					// it's filthy, get rid of it.
					$value = '';
				}
			}
		}

		// save value
		parent::update_value($post_id, $field, $value);
	}


	/*--------------------------------------------------------------------------------------
	*
	*	get_value
	*	- called from the edit page to get the value of your field. This function is useful
	*	if your field needs to collect extra data for your create_field() function.
	*
	*	@params
	*	- $post_id (int) - the post ID which your value is attached to
	*	- $field (array) - the field object.
	*
	*	@author Elliot Condon
	*	@since 2.2.0
	*
	*-------------------------------------------------------------------------------------*/

	function get_value($post_id, $field)
	{
		// get value
		$value = parent::get_value($post_id, $field);

		// format value

		// return value
		return $value;
	}


	/*--------------------------------------------------------------------------------------
	*
	*	get_value_for_api
	*	- called from your template file when using the API functions (get_field, etc).
	*	This function is useful if your field needs to format the returned value
	*
	*	@params
	*	- $post_id (int) - the post ID which your value is attached to
	*	- $field (array) - the field object.
	*
	*	@author Elliot Condon
	*	@since 3.0.0
	*
	*-------------------------------------------------------------------------------------*/

	function get_value_for_api($post_id, $field)
	{
		// get value
		$value = $this->get_value($post_id, $field);

		// format value

		// return value
		return $value;

	}
}

?>
