(function (Drupal) {
  Drupal.behaviors.downloadFiles = {
    attach: function (context, settings) {
      console.log('This is WORKING!');
    }
  };
})(Drupal);