<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Bootstrap 101 Template</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <style>
        body {
            min-height: 2000px;
            padding-top: 70px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-default navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">Zoom Integration</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li class="active"><a href="<?php echo base_url('oauth/zoom_meetings/') ?>">Home</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>

    <div class="container">

        <!-- Main component for a primary marketing message or call to action -->
        <div class="jumbotron">
            <ul class="list-group">

                <li class="list-group-item">
                    <p>Zoom Authorization</p>
                    <a href="<?php echo $zoom_authorize ?>" class="btn btn-primary">Authorize</a>
                </li>

                <?php if( $this->input->get("code") != null && !empty( $this->input->get("code") ) ): ?>

                <li class="list-group-item">
                    <p>Zoom Access Token</p>
                    <button onclick="get_token('<?php echo $this->input->get("code") ?>')" class="btn btn-primary">Get Token</button>
                    <br>

                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon" id="access_token">Access Token</span>
                            <textarea class="form-control" rows="6" placeholder="Access Token" aria-describedby="access_token" readonly></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon" id="refresh_token">Refresh Token</span>
                            <textarea class="form-control" rows="6" placeholder="Refresh Token" aria-describedby="refresh_token" readonly></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon" id="expires">Expires</span>
                            <input type="text" class="form-control" placeholder="Expires" aria-describedby="expires" readonly>
                        </div>
                    </div>
                </li>

                <li class="list-group-item">
                    <p>Zoom Refresh Token</p>
                    <button onclick="refresh_token()" class="btn btn-primary">Refresh Token</button>
                </li>

                <li class="list-group-item call-api">
                    <p>Call API</p>
                    <ul id="api" class="nav nav-pills">
                        <li role="presentation" class="active" onclick="users_info()"><a href="#">User Info</a></li>
                        <li role="presentation" onclick="users_meeting_info()"><a href="#">Meetings</a></li>
                        <li role="presentation" onclick="users_webinar_info()"><a href="#">Webinar</a></li>
                    </ul>
                    <br>

                    <pre id="myself" class="pre-scrollable"></pre>
                </li>

                <?php endif ?>

            </ul>
        </div>

    </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

    <script>
        focus = (target) => {
            $("html, body").animate({
                scrollTop: $(target).offset().top
            }, 1000);
        }

        get_token = (str) => {
            if( !str ) return alert("Please authorize & grant access token permissions");

            let target = "<?php echo base_url('oauth/zoom_meetings/get_token/') ?>";

            $.post(target + str, function(response) {
                // check response
                if( !response ) return alert("Failed to retreive token");

                // set value into form
                $("#access_token").next().val(response.access_token);
                $("#refresh_token").next().val(response.refresh_token);
                $("#expires").next().val(response.expires_in);
            })
        }

        refresh_token = () => {
            let target = "<?php echo base_url('oauth/zoom_meetings/refresh_token/') ?>";
            let token  = $("#refresh_token").next().val();

            if( !token ) return alert("Please authorize & grant access token permissions");

            $.post(target + token, function(response) {
                // check response
                if( !response ) return alert("Failed to retreive token");

                // set value into form
                $("#access_token").next().val(response.access_token);
                $("#refresh_token").next().val(response.refresh_token);
                $("#expires").next().val(response.expires_in);
            })
        }

        users_info = () => {
            let target = "<?php echo base_url('oauth/zoom_meetings/users_info/') ?>";
            let token  = $("#access_token").next().val();

            if( !token ) return alert("Please authorize & grant access token permissions");

            $.post(target + token, function(response) {
                // check response
                if( !response ) return alert("Failed to retreive info");

                // pretify json format
                let output = JSON.stringify(response, undefined, 2);
                // set value into form
                $("#myself").html( output );
                focus(".call-api");
            })
        }

        users_meeting_info = () => {
            let target = "<?php echo base_url('oauth/zoom_meetings/user_meeting_info/') ?>";
            let token  = $("#access_token").next().val();

            if( !token ) return alert("Please authorize & grant access token permissions");

            $.post(target + token, function(response) {
                // check response
                if( !response ) return alert("Failed to retreive info");

                // pretify json format
                let output = JSON.stringify(response, undefined, 2);
                // set value into form
                $("#myself").html( output );
                focus(".call-api");
            })
        }

        users_webinar_info = () => {
            let target = "<?php echo base_url('oauth/zoom_meetings/user_webinar_info/') ?>";
            let token  = $("#access_token").next().val();

            if( !token ) return alert("Please authorize & grant access token permissions");

            $.post(target + token, function(response) {
                // check response
                if( !response ) return alert("Failed to retreive info");

                // pretify json format
                let output = JSON.stringify(response, undefined, 2);
                // set value into form
                $("#myself").html( output );
                focus(".call-api");
            })
        }
    </script>
</body>
</html>