jQuery( function( $ ) {
    var FB_API_VERSION = 'v2.8', isUndefinedOrNull, normalizeFacebookResponse;

    isUndefinedOrNull = function (thing) {
      return (thing === void 0) || thing === null;
    };

    $('input.apply-with-facebook-button').click(function() {
        fblogin();
        $(this).attr('disabled','disabled');
        $(this).css('opacity', '0.7');
        return false;
    });

    function statusChangeCallback(response) {
        if ( response.status === 'connected' ) {
            $('.application_details, .wp-job-manager-application-details').slideUp();
            WPJMFacebook();
            $('.apply-with-facebook-details').slideDown().triggerHandler('wp-job-manager-application-details-show');
        } else if (response.status === 'not_authorized') {
            console.log('Facebook user is not authorized');
        } else {
            console.log('Please login to Facebook');
        }
    }

    function checkLoginState() {
        FB.getLoginStatus(function (response) {
            statusChangeCallback(response);
        });
    }

    function fblogin() {
        FB.login(function (response) {
            checkLoginState();
        }, {scope: 'public_profile,email,user_website,user_education_history,user_work_history,user_location,user_about_me'});
    }

    window.fbAsyncInit = function () {
        FB.init({
            appId: apply_with_facebook.appID,
            cookie: true,
            xfbml: true,
            version: FB_API_VERSION
        });
    };

    (function (d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s);
        js.id = id;
        js.src = "//connect.facebook.net/en_US/sdk.js";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));

    normalizeFacebookResponse = function normalizeFacebookResponse(response) {
      var theResponse = jQuery.extend({}, response);
      // fill missing data with sensible defaults
      if (isUndefinedOrNull(theResponse.name)) {
        theResponse.name = theResponse.first_name + ' ' + theResponse.last_name;
      }
      if (isUndefinedOrNull(theResponse.about)) {
        theResponse.about = '';
      }
      if (isUndefinedOrNull(theResponse.bio)) {
        theResponse.bio = response.about;
      }
      if (isUndefinedOrNull(theResponse.email)) {
        theResponse.email = '';
      }
      if (isUndefinedOrNull(theResponse.location)) {
        theResponse.location = {name: 'unknown'};
      }
      if (isUndefinedOrNull(theResponse.work)) {
        theResponse.work = [];
      }
      $.each(theResponse.work, function( key, value ) {
          if (isUndefinedOrNull(value.position)) {
            value.position = {name: 'Unknown position'};
          }
      });
      if (isUndefinedOrNull(theResponse.education)) {
        theResponse.education = [];
      }
      return theResponse;
    };

    function WPJMFacebook() {
        var fields = 'first_name,last_name,about,location,email,work,education,link';

        FB.api('/me', {fields: fields}, function(response) {
            var theResponse = normalizeFacebookResponse(response);

            $('#apply-with-facebook-profile-data').val( JSON.stringify( theResponse, null, '' ) );

            $('.apply-with-facebook-profile .profile-name').append(theResponse.name);
            $('.apply-with-facebook-profile .profile-bio').append(theResponse.about);
            $('.apply-with-facebook-profile .profile-location').append(theResponse.location.name);
            $('.apply-with-facebook-profile dd.profile-email').append(theResponse.email);

            // Work
            $.each(theResponse.work, function( key, value ) {
                if (typeof value.end_date == 'undefined') {
                    $('.apply-with-facebook-profile .profile-current-positions ul').append(
                        '<li>' + value.position.name + ' - ' + value.employer.name + '</li>'
                    );
                } else {
                    $('.apply-with-facebook-profile .profile-past-positions ul').append(
                        '<li>' + value.position.name + ' - ' + value.employer.name + '</li>'
                    );
                }
            });

            // Education
            $.each(theResponse.education, function( key, value ) {
                if (typeof value.concentration != 'undefined') {
                    var education = '<ul>';
                    $.each(value.concentration, function( key, value ) {
                        education += '<li>' + value.name + '</li>';
                    });
                    education += '</ul>';
                } else {
                    var education = '';
                }
                if ( typeof value.year != 'undefined' ) {
                    var year = ' (' + value.year.name + ')';
                } else {
                    var year = '';
                }
                $('.apply-with-facebook-profile .profile-educations ul').append(
                    '<li>' + value.type + ' - ' + value.school.name + year + education + '</li>'
                );
            });
        });
        FB.api('/me/picture/?redirect=0&type=normal&width=100', function (response) {
            $('.apply-with-facebook-profile img').attr('src', response.data.url);
            $('#apply-with-facebook-profile-picture').val( response.data.url );
        });
    }
});
