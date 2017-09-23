/**
 * @file
 * Preview for the Mayo theme.
 */
(function ($, Drupal, drupalSettings) {

  "use strict";

  Drupal.color = {
    logoChanged: false,
    bgChanged: false,
    callback: function(context, settings, form, farb, height, width) {

      // Move the color wheel downwards
      form.find('.color-placeholder').css('margin-top', '300px'); // adjusted based on Seven theme

      // Apply layout style
      if (drupalSettings.color.layout_style == 2) {
        // No page margin to header and footer
        form.find('.color-preview #preview-page').css('padding', '0px');
        form.find('.color-preview #preview-main').css('padding', '0px 10px');
      }
      // Apply sidebar layout style
      if (drupalSettings.color.sb_layout_style == 3) { // right sidebar
        form.find('.color-preview .sidebar').css('float', 'right');
        if (drupalSettings.color.layout_style == 2) {
          form.find('.color-preview .sidebar').css('margin-right', '20px');
        }
        else {
          form.find('.color-preview .sidebar').css('margin-right', '0px');
        }
        form.find('.color-preview #preview-content').css('margin-left', '0px');
        form.find('.color-preview #preview-content').css('margin-right', '10px');
      }
      else {
        form.find('.color-preview .sidebar').css('float', 'left');
        form.find('.color-preview #preview-content').css('margin-left', '10px');
        form.find('.color-preview #preview-content').css('margin-right', '0px');
      }

      // Apply base vertical margin
      form.find('.color-preview #preview-page-wrapper').css('padding-top', drupalSettings.color.base_vmargin);
      form.find('.color-preview #preview-page-wrapper').css('padding-bottom', drupalSettings.color.base_vmargin);

      // Change the logo to be the real one.
      if (!this.logoChanged) {
        $('.color-preview .color-preview-logo img').attr('src', drupalSettings.color.logo_path);
        this.logoChanged = true;
      }
      // Remove the logo if the setting is toggled off.
      if (drupalSettings.color.logo_path === null) {
        $('div').remove('.color-preview-logo');
      }

      // Base background.
      form.find('#preview-page-wrapper').css('background-color', form.find('.js-color-palette input[name="palette[wall]"]').val());

      // Page background.
      form.find('#preview-page').css('background-color', form.find('.js-color-palette input[name="palette[bg]"]').val());


      // Generic text and link
      form.find('#preview').css('color', form.find('.js-color-palette input[name="palette[text]"]').val());
      form.find('.color-preview table tr td').css('color', form.find('.js-color-palette input[name="palette[text]"]').val());
      form.find('.color-preview table tr th').css('color', form.find('.js-color-palette input[name="palette[text]"]').val());
      form.find('.color-preview a').css('color', form.find('.js-color-palette input[name="palette[link]"]').val());


      // Page title background.
      form.find('#preview-page-title').css('background-color', form.find('.js-color-palette input[name="palette[pagetitle]"]').val());

      // Page title text
      form.find('#preview-page-title').css('color', form.find('.js-color-palette input[name="palette[pagetitletext]"]').val());


      // Menu divider
      if (drupalSettings.color.menubar_style == 1) {
        form.find('.color-preview #preview-navigation').css('border-bottom-color', $('.js-color-palette input[name="palette[menudivider]"]', form).val());
        form.find('.color-preview #preview-navigation ul li').css('border-right-color', $('.js-color-palette input[name="palette[menudivider]"]', form).val());
        form.find('.color-preview .highlight').css('background-color', $('.js-color-palette input[name="palette[highlight]"]', form).val());
      }
      else if (drupalSettings.color.menubar_style == 2) {
        form.find('.color-preview #preview-navigation a').css('color', '#dddddd');
        form.find('.color-preview .highlight').css('background-color', '#444444');
      }

      // Node background.
      form.find('.color-preview .node').css('backgroundColor', $('.js-color-palette input[name="palette[bg]"]').val());

      // Node border
      form.find('.color-preview .node').css('border-color', $('.js-color-palette input[name="palette[nodeborders]"]').val());

      // Node divider
      form.find('.color-preview .node h2').css('border-bottom-color', $('.js-color-palette input[name="palette[nodedivider]"]').val());

      // Sticky node background.
      form.find('.color-preview .node--sticky').css('background-color', $('.js-color-palette input[name="palette[stickynode]"]').val());


      // Table background
      form.find('.color-preview table tr th').css('background-color', $('.js-color-palette input[name="palette[tableheader]"]').val());
      form.find('.color-preview table tr.even td').css('background-color', $('.js-color-palette input[name="palette[even]"]').val());
      form.find('.color-preview table tr.odd td').css('background-color', $('.js-color-palette input[name="palette[node]"]').val());
      form.find('.color-preview table tr th').css('border-color', $('.js-color-palette input[name="palette[node]"]').val());

      // Sidebar background.
      form.find('.color-preview .sidebar .block').css('background-color', $('.js-color-palette input[name="palette[sidebar]"]').val());

      // Sidebar border
      form.find('.color-preview .sidebar .block').css('border-color', $('.js-color-palette input[name="palette[sidebarborders]"]').val());

      // Sidebar divider
      form.find('.color-preview .sidebar h2').css('border-bottom-color', $('.js-color-palette input[name="palette[sidebardivider]"]').val());

      // Sidebar text and link
      form.find('.color-preview .sidebar .block').css('color', $('.js-color-palette input[name="palette[sidebartext]"]').val());
      form.find('.color-preview .sidebar a').css('color', $('.js-color-palette input[name="palette[sidebarlink]"]').val());


      // Footer background.
      form.find('.color-preview #preview-footer-wrapper').css('background-color', $('.js-color-palette input[name="palette[footer]"]').val());

      // Footer text and link
      form.find('.color-preview #preview-footer-wrapper').css('color', $('.js-color-palette input[name="palette[footertext]"]').val());
      form.find('.color-preview #preview-footer-wrapper a').css('color', $('.js-color-palette input[name="palette[footerlink]"]').val());

      if (drupalSettings.color.header_bg_file) {
        if (!this.bgChanged) {
          // Change the header_bg_file to be the real one.
          this.bgChanged = true;
          // Header background image
          form.find('.color-preview #preview-header').attr('style', 'border: none; background-image: url(' + drupalSettings.color.header_bg_file + '); background-position: ' + drupalSettings.color.header_bg_alignment + ';');
        }
      }
      else {
        // CSS3 Gradients.
        var gradient_start = $('.js-color-palette input[name="palette[left]"]', form).val();
        var gradient_end = $('.js-color-palette input[name="palette[right]"]', form).val();

        // Header background
        $('.color-preview #preview-header', form).attr('style', "background-color: " + gradient_start + "; background-image: -webkit-gradient(linear, left top, right top, from(" + gradient_start + "), to(" + gradient_end + ")); background-image: -moz-linear-gradient(0deg, " + gradient_start + ", " + gradient_end + "); filter:progid:DXImageTransform.Microsoft.Gradient(StartColorStr=" + gradient_start + ", EndColorStr=" + gradient_end + ", GradientType=1); -ms-filter:\"progid:DXImageTransform.Microsoft.gradient(startColorstr=" + gradient_start + ", endColorstr=" + gradient_end + ", GradientType=1)\";");

        if (drupalSettings.color.layout_style == 2) {
          form.find('.color-preview #preview-header').css('border', 'none');
        }
        else {
          // Header border
          form.find('.color-preview #preview-header').css('border-color', form.find('.js-color-palette input[name="palette[headerborders]"]').val());
          form.find('.color-preview #preview-header').css('border-width', drupalSettings.color.header_border_width);
        }
      }
      if (drupalSettings.color.header_watermark > 0) {
        var url = '/themes/mayo/images/pat-' + drupalSettings.color.header_watermark + '.png';
        form.find('.color-preview #preview-header-watermark').attr('style', 'background-image: url(' + url + ');');
      }

      // Title and slogan
      form.find('.color-preview #preview-name-and-slogan').css('color', form.find('.js-color-palette input[name="palette[titleslogan]"]').val());
      form.find('.color-preview #preview-name-and-slogan a').css('color', form.find('.js-color-palette input[name="palette[titleslogan]"]').val());
    }
  };
})(jQuery, Drupal, drupalSettings);
