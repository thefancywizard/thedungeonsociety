<?php

use Drupal\Component\Utility\Html;
use Drupal\Core\Form\FormStateInterface;
use Drupal\system\Form\ThemeSettingsForm;
use Drupal\file\Entity\File;
use Drupal\Core\Url;
use Drupal\Core\File\FileSystemInterface;

function xtrax_form_system_theme_settings_alter(&$form, FormStateInterface &$form_state)
{
  global $base_url;
  $form['settings'] = array(
    '#type' => 'details',
    '#title' => t('Theme settings'),
    '#open' => TRUE,
  );
  $form['settings']['header'] = array(
    '#type' => 'details',
    '#title' => t('Header settings'),
    '#open' => FALSE,
  );
  if (!empty(theme_get_setting('logo_image', 'xtrax'))) {
    $form['settings']['header']['logo_preview'] = array(
      '#prefix' => '<div>',
      '#markup' => '<img src="' . $base_url . theme_get_setting('logo_image', 'xtrax') .
        '" height="100" width="100" />',
      '#suffix' => '</div>',
    );
  }
  $form['settings']['header']['logo_image'] = array(
    '#type' => 'hidden',
    //'#title' => t('URL of the background image'),
    '#default_value' => theme_get_setting('logo_image'),
    '#size' => 40,
    //'#disabled' => 'disabled',
    '#maxlength' => 512,
  );

  $form['settings']['header']['logo_image_upload'] = array(
    '#type' => 'file',
    '#title' => t('Upload logo image'),
    '#size' => 40,
    '#attributes' => array('enctype' => 'multipart/form-data'),
    '#description' => t('If you don\'t jave direct access to the server, use this field to upload your logo image. Uploads limited to .png .gif .jpg .jpeg .apng .svg extensions'),
    '#element_validate' => array('logo_image_validate'),
  );

  if (!empty(theme_get_setting('logo_image_transparent', 'xtrax'))) {
    $form['settings']['header']['logo_transparent_preview'] = array(
      '#prefix' => '<div style="background-color: #cccccc">',
      '#markup' => '<img src="' . $base_url . theme_get_setting('logo_image_transparent', 'xtrax') .
        '" height="100" width="100" />',
      '#suffix' => '</div>',
    );
  }
  $form['settings']['header']['logo_image_transparent'] = array(
    '#type' => 'hidden',
    //'#title' => t('URL of the background image'),
    '#default_value' => theme_get_setting('logo_image_transparent'),
    '#size' => 40,
    //'#disabled' => 'disabled',
    '#maxlength' => 512,
  );

  $form['settings']['header']['logo_image_transparent_upload'] = array(
    '#type' => 'file',
    '#title' => t('Upload logo transparent'),
    '#size' => 40,
    '#attributes' => array('enctype' => 'multipart/form-data'),
    '#description' => t('If you don\'t jave direct access to the server, use this field to upload your logo transparent image. Uploads limited to .png .gif .jpg .jpeg .apng .svg extensions'),
    '#element_validate' => array('logo_image_transparent_validate'),
  );
  $form['settings']['header']['social_networks'] = array(
    '#type' => 'textarea',
    '#title' => t('Social Networks'),
    '#default_value' => theme_get_setting('social_networks', 'xtrax') ?? '',
    '#description' => t('For Example') . ':<br/>' . '<pre><code><span><</span>li<span>></span><span><</span>a href="#"<span>></span><span><</span>i class="fab fa-facebook-f"<span>></span><span><</span>/i<span>></span><span><</span>/a<span>></span><span><</span>/li<span>></span></code></pre>'
  );

  // Blog settings
  $form['settings']['blog'] = array(
    '#type' => 'details',
    '#title' => t('Blog settings'),
    '#open' => FALSE,
  );

  $form['settings']['blog']['blog_listing'] = array(
    '#type' => 'details',
    '#title' => t('Blog listing'),
    '#open' => FALSE,
  );
  $form['settings']['blog']['blog_listing']['enable_demo'] = array(
    '#type' => 'checkbox',
    '#title' => t('Enable demo'),
    '#default_value' => theme_get_setting('enable_demo', 'xtrax') ? theme_get_setting('enable_demo', 'xtrax') : false,
  );

  $form['settings']['blog']['blog_listing']['blog_listing_style'] = array(
    '#type' => 'select',
    '#title' => t('Listing style'),
    '#options' => array(
      'layout_1' => t('Layout 1 (Default)'),
      'layout_2' => t('Layout 2'),
    ),
    '#default_value' => theme_get_setting('blog_listing_style', 'xtrax') ?? 'layout_1',
  );

  if (!empty(theme_get_setting('blog_background_image', 'xtrax'))) {
    $form['settings']['blog']['blog_listing']['blog_background_image_preview'] = array(
      '#prefix' => '<div>',
      '#markup' => '<img src="' . $base_url . theme_get_setting('blog_background_image', 'xtrax') .
        '" height="100" width="100" />',
      '#suffix' => '</div>',
    );
  }
  $form['settings']['blog']['blog_listing']['blog_background_image'] = array(
    '#type' => 'hidden',
    //'#title' => t('URL of the background image'),
    '#default_value' => theme_get_setting('blog_background_image'),
    '#size' => 40,
    //'#disabled' => 'disabled',
    '#maxlength' => 512,
  );

  $form['settings']['blog']['blog_listing']['blog_background_image_upload'] = array(
    '#type' => 'file',
    '#title' => t('Upload blog background image'),
    '#size' => 40,
    '#attributes' => array('enctype' => 'multipart/form-data'),
    '#description' => t('If you don\'t jave direct access to the server, use this field to upload your blog background image. Uploads limited to .png .gif .jpg .jpeg .apng .svg extensions'),
    '#element_validate' => array('blog_background_image_validate'),
  );
  $form['settings']['contact'] = array(
    '#type' => 'details',
    '#title' => t('Contact settings'),
    '#open' => FALSE,
  );
//  $form['settings']['contact']['contact_background_image'] = array(
//
//    '#type' => 'managed_file',
//    '#title' => t('Background image upload'),
//
//    '#upload_location' => \Drupal::config('system.file')->get('default_scheme') . '://background_images',
//    '#default_value' => theme_get_setting('contact_background_image', 'xtrax'),
//    '#upload_validators' => array(
//      'file_validate_extensions' => array('gif png jpg jpeg apng svg'),
//      //'file_validate_image_resolution' => array('960x400','430x400')
//      '#progress_message' => 'Uploading ...',
//    ),
//    '#description' => t('If you don\'t jave direct access to the server, use this field to upload your background image. Uploads limited to .png .gif .jpg .jpeg .apng .svg extensions'),
//  );

  if (!empty(theme_get_setting('contact_background_image', 'xtrax'))) {
    $form['settings']['contact']['contact_background_image_preview'] = array(
      '#prefix' => '<div>',
      '#markup' => '<img src="' . $base_url . theme_get_setting('contact_background_image', 'xtrax') .
        '" height="100" width="100" />',
      '#suffix' => '</div>',
    );
  }
  $form['settings']['contact']['contact_background_image'] = array(
    '#type' => 'hidden',
    //'#title' => t('URL of the background image'),
    '#default_value' => theme_get_setting('contact_background_image'),
    '#size' => 40,
    //'#disabled' => 'disabled',
    '#maxlength' => 512,
  );

  $form['settings']['contact']['contact_background_image_upload'] = array(
    '#type' => 'file',
    '#title' => t('Upload contact background image'),
    '#size' => 40,
    '#attributes' => array('enctype' => 'multipart/form-data'),
    '#description' => t('If you don\'t jave direct access to the server, use this field to upload your contact background image. Uploads limited to .png .gif .jpg .jpeg .apng .svg extensions'),
    '#element_validate' => array('contact_background_image_validate'),
  );


  $form['settings']['contact']['contact_subtitle'] = array(
    '#type' => 'textarea',
    '#title' => t('Subtitle'),
    '#default_value' => theme_get_setting('contact_subtitle', 'xtrax'),
  );
  $form['settings']['contact']['contact_description'] = array(
    '#type' => 'textarea',
    '#title' => t('Description'),
    '#default_value' => theme_get_setting('contact_description', 'xtrax'),
  );

  $form['settings']['contact']['contact_address_info'] = array(
    '#type' => 'textarea',
    '#title' => t('Contact address info'),
    '#default_value' => theme_get_setting('contact_address_info', 'xtrax'),
  );
  $form['settings']['contact']['geo_settings'] = array(
    '#type' => 'details',
    '#title' => t('Location'),
    '#open' => TRUE,
  );
  $form['settings']['contact']['geo_settings']['google_api_key'] = array(
    '#type' => 'textfield',
    '#title' => t('Google API key'),
    '#default_value' => theme_get_setting('google_api_key', 'xtrax'),
  );
  $form['settings']['contact']['geo_settings']['contact_lat'] = array(
    '#type' => 'textfield',
    '#title' => t('Lat'),
    '#default_value' => theme_get_setting('contact_lat', 'xtrax'),
  );
  $form['settings']['contact']['geo_settings']['contact_lng'] = array(
    '#type' => 'textfield',
    '#title' => t('Lng'),
    '#default_value' => theme_get_setting('contact_lng', 'xtrax'),
  );

  $form['settings']['general_setting'] = array(
    '#type' => 'details',
    '#title' => t('General Settings'),
    '#open' => FALSE,
  );
  $form['settings']['general_setting']['general_setting_tracking_code'] = array(
    '#type' => 'textarea',
    '#title' => t('Tracking Code'),
    '#default_value' => theme_get_setting('general_setting_tracking_code', 'xtrax'),
  );
  $form['settings']['general_setting']['custom_css'] = array(
    '#type' => 'textarea',
    '#title' => t('Custom CSS'),
    '#default_value' => theme_get_setting('custom_css', 'xtrax'),
    '#description'  => t('<strong>Example:</strong><br/>h1 { font-family: \'Metrophobic\', Arial, serif; font-weight: 400; }'),
  );
}

// logo
function logo_image_validate($element, FormStateInterface $form_state)
{
  global $base_url;

  $validators = array('file_validate_extensions' => array('png gif jpg jpeg apng svg'));
  $file = file_save_upload('logo_image_upload', $validators, "public://", null, FileSystemInterface::EXISTS_RENAME);

  if (!empty($file)) {
    // change file's status from temporary to permanent and update file database
    if ((is_object($file[0]) == 1)) {
      $file[0]->status = FILE_STATUS_PERMANENT;
      $file[0]->save();
      $uri = $file[0]->getFileUri();
      $file_url = file_create_url($uri);
      $file_url = str_ireplace($base_url, '', $file_url);
      $form_state->setValue('logo_image', $file_url);
    }
  }
}

// logo transparent
function logo_image_transparent_validate($element, FormStateInterface $form_state)
{
  global $base_url;

  $validators = array('file_validate_extensions' => array('png gif jpg jpeg apng svg'));
  $file = file_save_upload('logo_image_transparent_upload', $validators, "public://", null, FileSystemInterface::EXISTS_RENAME);

  if (!empty($file)) {
    // change file's status from temporary to permanent and update file database
    if ((is_object($file[0]) == 1)) {
      $file[0]->status = FILE_STATUS_PERMANENT;
      $file[0]->save();
      $uri = $file[0]->getFileUri();
      $file_url = file_create_url($uri);
      $file_url = str_ireplace($base_url, '', $file_url);
      $form_state->setValue('logo_image_transparent', $file_url);
    }
  }
}

// blog background image
function blog_background_image_validate($element, FormStateInterface $form_state)
{
  global $base_url;

  $validators = array('file_validate_extensions' => array('png gif jpg jpeg apng svg'));
  $file = file_save_upload('blog_background_image_upload', $validators, \Drupal::config('system.file')->get('default_scheme') . '://background_images', null, FileSystemInterface::EXISTS_RENAME);

  if (!empty($file)) {
    // change file's status from temporary to permanent and update file database
    if ((is_object($file[0]) == 1)) {
      $file[0]->status = FILE_STATUS_PERMANENT;
      $file[0]->save();
      $uri = $file[0]->getFileUri();
      $file_url = file_create_url($uri);
      $file_url = str_ireplace($base_url, '', $file_url);
      $form_state->setValue('blog_background_image', $file_url);
    }
  }
}

// contact background image
function contact_background_image_validate($element, FormStateInterface $form_state)
{
  global $base_url;

  $validators = array('file_validate_extensions' => array('png gif jpg jpeg apng svg'));
  $file = file_save_upload('contact_background_image_upload', $validators, \Drupal::config('system.file')->get('default_scheme') . '://background_images', null, FileSystemInterface::EXISTS_RENAME);

  if (!empty($file)) {
    // change file's status from temporary to permanent and update file database
    if ((is_object($file[0]) == 1)) {
      $file[0]->status = FILE_STATUS_PERMANENT;
      $file[0]->save();
      $uri = $file[0]->getFileUri();
      $file_url = file_create_url($uri);
      $file_url = str_ireplace($base_url, '', $file_url);
      $form_state->setValue('contact_background_image', $file_url);
    }
  }
}
