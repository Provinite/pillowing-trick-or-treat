<?php
function show_top_menu($loggedIn, $icon, $username) {
    $ret = '<div id="topmenu">';
    if ($loggedIn) {
        $ret .= '
        <img src="' . $icon . '" class="usericon" />
        <div class="username">
            ' . $username . '
        </div>
        <ul>
            <li class="prizeLink txtBtn" data-idx="4">My Prizes</li>
            <li class="logoutLink">Log Out</li>
        </ul>
        ';
    } else {
        $ret .= '<img src="/apps/assets/raffle/img/login-with-da-small.png" class="login loginLink" />';
    }
    $ret .= '</div>';
    return $ret;
}
?><!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <title>CloverCoin - Raffle</title>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/fullPage.js/2.7.4/jquery.fullPage.js"></script>

    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/fullPage.js/2.7.4/jquery.fullPage.css" />
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <link href='//fonts.googleapis.com/css?family=Roboto:400,300' rel='stylesheet' type='text/css'>
    <script type="text/javascript">
        var tot_url = "<?php echo site_url('test/trickortreat'); ?>";
        (function($) {
            "use strict";

            var goto = function(el, n, props) {
                n = n % el.data('tr-maxidx');
                el.data('tr-idx', n);
                el.children().first().stop().fadeOut(props.outTime, function() {
                   setHtml($(this).parent());
                   $(this).fadeIn(props.inTime);
                });
            };

            var setHtml = function(el) {
                el.children().first().children().first().detach();
                el.children().first().append(el.data('tr-htmls')[el.data('tr-idx')]);
            };

            $.fn.extend({
                textRotate: function(props) {
                    return this.each(function() {
                        var el = $(this);
                        var idx;
                        var objProps = el.data('tr-props');
                        if (props == null || props instanceof Object) {

                            var htmls = [];

                            el.data('tr-props', props);
                            if (props == null) {
                                el.data('tr-props', {});
                            }

                            el.children().each(function(i, e) {
                                htmls.push($(e));
                                $(e).detach();
                            });

                            el.data('tr-idx', 0);
                            el.data('tr-maxidx', htmls.length);
                            el.data('tr-htmls', htmls);

                            el.children().remove();
                            el.append('<div></div>');

                            goto(el, 0, props);

                        } else if (props == "next") {
                            idx = el.data('tr-idx');
                            goto(el, idx + 1, objProps);
                        } else if (!isNaN(parseInt(props)) && isFinite(props)) {
                            idx = parseInt(props);
                            goto(el, idx, objProps);
                        }
                    })
                }
            })
        })(window.jQuery);

        (function($) {
            var tickDown = function(el, callback) {
                var h, m, s;
                h = $(el).data('countdown-h');
                m = $(el).data('countdown-m');
                s = $(el).data('countdown-s');

                s = s - 1;

                if (s < 0) {
                    s = 59;
                    m = m - 1;
                    if (m < 0) {
                        m = 59;
                        h = h - 1;
                        if (h < 0) {
                            callback.bind(el)();
                            clearInterval($(el).data('countdown-interval'));
                        }
                    }
                }

                $(el).text(
                    ((String(h).length == 1) ? '0' + h : h) + ':' +
                    ((String(m).length == 1) ? '0' + m : m) + ':' +
                    ((String(s).length == 1) ? '0' + s : s)
                );

                $(el).data('countdown-h', h);
                $(el).data('countdown-m', m);
                $(el).data('countdown-s', s);
            };
            $.fn.extend({
                countdown: function(h, m, s, done) {
                    return $(this).each(function() {
                        var el = $(this);
                        $(el).data('countdown-h', h);
                        $(el).data('countdown-m', m);
                        $(el).data('countdown-s', s);

                        $(el).data('countdown-interval', setInterval(function() {
                            tickDown(this, done)
                        }.bind(el), 1000));
                    });
                }
            })
        })(window.jQuery);

        var mobileAndTabletcheck = function() {
            var check = false;
            (function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino|android|ipad|playbook|silk/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4)))check = true})(navigator.userAgent||navigator.vendor||window.opera);
            return check;
        };
        var autoscroll = !mobileAndTabletcheck();
        $(document).ready(function() {
            $('div#no_script').remove();

            $('#fullpage').fullpage({
                afterLoad: function() {
                    $('body').trigger('resize');
                },
                autoScrolling: autoscroll
            });
            var textRotator = $('.txtrotate');

            $('.loginLink').click(function() {
                var lnkUrl = '<?php echo site_url('login'); ?>';
                lnkUrl = lnkUrl + '?return_url=' + window.location;

                var wHeight = $(window).height();
                var newTop = wHeight / 2;
                $('#loading_overlay > div').css("top", newTop);
                $('#loading_overlay').show();

                window.location.href = lnkUrl;
            });

            $('.logoutLink').click(function() {
                var lnkUrl = '<?php echo site_url('login/logout'); ?>';
                lnkUrl = lnkUrl + '?return_url=' + window.location;
                window.location.href = lnkUrl;
            });

            doBtnClick = function() {
                if ($(this).hasClass('txtBtnActive')) { return; }
                $('.txtBtn').removeClass('txtBtnActive');
                var idx = $(this).data('idx');
                $(textRotator).textRotate(idx);
                if ($(this).closest('.buttons').length > 0)
                    $(this).addClass('txtBtnActive');
                window.location.hash = '#main' + idx;
            }

            $('.txtBtn').click(doBtnClick);

            $("#tot_pane").one('click', function() {

                var startKnocks = Date.now();
                var minDelay = 1500;

                $(this).children().each(function(i) {
                    var delay = Math.random() * 500;
                    setTimeout(function() {
                        $(this).css('display', 'block');
                        //$(this).css('font-size', '0');
                        $(this).fadeTo(0, 0);
                        $(this).animate({
                        //    fontSize: '20px',
                            opacity: '1'
                        }, 700).animate({
                        //    fontSize: '0px',
                            opacity: '0'
                        }, 500);
                    }.bind(this), delay+(i*500));
                });

                var showTimer = function(param) {
                    param = param.split(':');
                    var hours = param[0];
                    var minutes = param[1];
                    var seconds = param[2];
                    $('#tot_timer').countdown(hours, minutes, seconds);
                    $('#tot_pane_wrapper').fadeOut(500, function() {
                        $('#tot_timer_wrapper').fadeIn(500);
                    });
                };

                var showAlert = function(text) {
                    alert(text);
                };

                var showPrize = function(name, desc, imageurl) {
                    $('#tot_prize_name').text(name);
                    $('#tot_prize_desc').text(desc);
                    $('#tot_prize_image').attr('src', imageurl);
                    $('#tot_pane_wrapper').fadeOut(500, function() {
                        $('#tot_prize_wrapper').fadeIn(500);
                    });
                };

                $.ajax({
                    url: tot_url,
                    method: "GET",
                    dataType: "json",
                    success: function(data) {
                        var iv;
                        if (data.result == false) { //can't ToT yet
                            iv = setInterval(function() {
                                if (Date.now() - startKnocks >= minDelay) {
                                    clearInterval(iv);
                                    showTimer(data.time_until_reset);
                                }
                            }, 100);
                        } else {
                            var name = data.prize;
                            var desc = data.description;
                            var image = data.image;

                            iv = setInterval(function() {
                                if (Date.now() - startKnocks >= minDelay) {
                                    clearInterval(iv);
                                    showPrize(name, desc, image);
                                }
                            }, 100);
                        }
                    },
                    error: function(xhr, textStatus, errorThrown) {
                        if (xhr.status == "401") { //not logged in
                            showAlert("You'll need to log in to Trick or Treat!");
                        } else { //something is wrong
                            showAlert("Uh oh! Something went wrong with your request. Try logging out and back in and trying again.");
                        }
                    }
                });
            });

            $('#topmenu').click(function() {
                var border = parseInt($(this).css("borderBottomLeftRadius"));
                border = 25 - border;
                $(this).find('ul').slideToggle();

            });

            $(textRotator).textRotate({
                outTime: 500,
                inTime: 500
            });

            var hash = window.location.hash;
            if (hash.substr(0, 5) == "#main" && hash != "#main") {
                var idx = hash.substr(5);
                $('.txtBtn').each(function() {
                    if (idx == $(this).data('idx')) {
                        doBtnClick.bind($(this))();
                    }
                });
                window.location.hash = '#main';
                setTimeout(function() {
                    if (window.location.hash == '#main'){
                        window.location.hash = '#main' + idx;
                    }
                }, 2000);
            }
        });
    </script>
    <style type="text/css">
        a {
            text-decoration:none;
        }
        body {
            font-family: "Roboto", "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif;
        }

        @media screen and (orientation:portrait) {
            #fullpage div.section.first {
                background: #252B2D url('/apps/assets/raffle/img/largebg_1080_1920.png') top center/contain no-repeat;
            }
        }
        @media screen and (orientation:landscape) {
            #fullpage div.section.first {
                background: #252B2D url('/apps/assets/raffle/img/largebg_1920_1080.png') top center/contain no-repeat;
            }
        }

        #fullpage div.section.first {
            text-align:center;
        }
        #fullpage div.section.first div.fp-tableCell {
            vertical-align:bottom;
        }
        #fullpage div.section.second {
            background: #252B2D;
            text-align:center;
            color:#96ACAD;
        }

        #infoBox {
            display:inline-block;
            text-align:center;
            margin:0 auto;
        }
        #infoArrow {
            color: white;
            font-size:30pt;
            text-shadow:2px 2px 3px #000;
        }
        #infoText {
            color: white;
            font-size:20pt;
            display:block;
            text-shadow:2px 2px 3px #000;
        }

        h1 {
            text-align:center;
            margin-top: 0;
        }

        div.buttons .txtBtn {
            font-size:30pt;
        }
        i + .label {
            font-size:8px;
            display:block;
            margin:0 auto;
            width:48px;
        }



        div.buttons .txtBtn:not(.txtBtnActive) {
            cursor: pointer;
        }

        div.buttons .txtBtn.txtBtnActive {
            color:#E87952;
        }
        div.buttons .txtBtn.txtBtnActive + .label {
            color:#E87952;
        }

        div.buttons .txtBtn:hover {
            color:#E87952;
        }

        div.buttons .txtBtn.fa-deviantart:hover {
            color: #05CC47;
        }
        div.buttons .txtBtn.fa-deviantart:hover + .label {
            color: #05CC47;
        }

        div.buttons .txtBtn.fa-deviantart.txtBtnActive {
            color: #05CC47;
        }

        div.buttons .txtBtn.fa-deviantart.txtBtnActive + .label {
            color: #05CC47;
        }

        .txtrotate {
            max-width:800px;
            margin:0 auto;
            text-align:justify;
            padding-left:40px;
            padding-right:40px;
            min-height:500px;
        }

        .txtrotate h1 {
            text-align:center;
            font-size:24pt;
            margin-bottom:10px;
            color: #E87952;
        }

        hr {
            margin-bottom:20px;
            color: #343E42;
            background-color: #343E42;
            width:300px;
            height:1px;
            border: none;
            position: relative;
        }

        div.buttons {
            border-radius:40px;
            height:50px;
            text-align:center;
            vertical-align: middle;
            background-color:#3D484C;
            width:360px;
            margin:5px auto;
            box-sizing:border-box;
            padding-top:5px;
        }


        div.buttons > div {
            width: 49px;
            float: left;
            margin:0 20px;
            position: relative;
            top: -5px;
        }

        div.da_button {
            position:relative;
            display:inline-block;
            height:80px;
            width:250px;
            z-index:1;
            cursor:pointer;
        }
        div.da_button img {
            z-index:1;
        }
        div.da_button:before {
            content:' ';
            position:absolute;
            top:-5px;
            left:-25px;
            width: 300px;
            height: 72px;
            -webkit-transform: skew(-28deg);
            -moz-transform: skew(-28deg);
            -o-transform: skew(-28deg);
            -ms-transform: skew(-28deg);
            background: #3D484C;
            z-index:-1;
        }

        .color-da-green {
            color: #05CC47;
        }

        div#credits {
            width:400px;
            margin:0 auto;
        }

        div#credits div.row div.name {
            float:left;
            width:150px;
            font-weight:bold;
        }

        div#credits div.row div.name a:hover {
            color: #B83349;
        }
        div#credits div.row div.credit {
            float:left;
            width: 250px;
        }

        a {
            color: #E87952;
        }

        div#topmenu ul {
            list-style-type: none;
            padding: 0;
            display: none;
        }

        div#topmenu ul li {
            background-color: #62767C;
        }

        div#topmenu ul li + li {
            margin-top:5px;
        }

        div#topmenu ul li:hover {
            color: #E87952;
        }

        div#topmenu {
            position:absolute;
            top:0;
            right:0;
            padding-top:5px;
            padding-left: 15px;
            padding-right:15px;
            vertical-align:middle;
            z-index:1;
            background-color:#62767C;
            color: #FFF;
            min-height:50px;
            box-sizing:border-box;
            border-radius:25px;
            margin-top:5px;
            margin-right:5px;
            opacity: 0.75;
            cursor: pointer;

            -webkit-transition: all 0.2s ease-in-out;
            -moz-transition: all 0.2s ease-in-out;
            -o-transition: all 0.2s ease-in-out;
            transition: all 0.2s ease-in-out;
        }

        @media screen and (max-width: 600px) {
            div#topmenu {
                width:100%;
                top:0;
                left:0;
                border-radius:0;
                margin:0;
                opacity: 1;
            }

            div.section:not(.first) {
                padding-top:30px;
            }

            #infoBox #infoText {
                display: none;
            }
        }

        div#topmenu:hover {
            opacity: 1;
            -webkit-transition: all 0.2s ease-in-out;
            -moz-transition: all 0.2s ease-in-out;
            -o-transition: all 0.2s ease-in-out;
            transition: all 0.2s ease-in-out;
        }
        div.username {
            display:inline-block;
            line-height: 40px;
            vertical-align: top;
            height: 40px;
        }
        div.username span {
            font-size:10px;
            color:#FFF;
        }
        img.usericon {
            width: 40px;
            height:40px;
        }

        div#topmenu img.login {
            cursor:pointer;
        }

        .logoutLink {
            cursor: pointer;
        }

        .loginLink {
            cursor: pointer;
        }

        ul.txtrotate {
            list-style-type: none;
        }

        img.header {
            margin:0 auto;
            display:block;
            height:150px;
        }

        div.header {
            margin:0 auto;
            display: block;
            height: 150px;
        }
    div#loading_overlay {
        display: none;
        z-index:1;
        width:100%;
        height:100%;
        position:fixed;
        left:0;
        top:0;
        text-align:center;
        padding-top:50%;
        background-color: rgba(0, 0, 0, 0.7);
    }

    div#loading_overlay > div {
        position:absolute;
        top:50%;
        left:50%;
        width: 200px;
        height:100px;
        text-align:center;
        margin-top:-50px;
        margin-left:-100px;
        color:#E87952;
        font-weight:bold;
        font-size:18pt;
    }

    div#tot_pane_wrapper {
        text-align:center;
    }

    div#tot_pane_wrapper p {
        margin:0;
        position: relative;
        top:-8px;
    }

    div#tot_pane {
        margin: 0 auto;
        background-image: url('/apps/assets/raffle/img/door.png');
        background-size: 100% 100%;
        width:350px;
        height:388px;
        cursor: pointer;
        position: relative;
    }

    #tot_pane > div {
        display:none;
        width:200px;
        height:30px;
        color:#FFF;
        font-family: "Lucida Console", Monaco, monospace;
        text-transform:uppercase;
        font-size:20px;
        letter-spacing: 3px;
        position: absolute;
        text-align:center;
    }

    #tot_pane > div:nth-child(1) {
        left: 25%;
        top:35%;
    }

    #tot_pane > div:nth-child(2) {
        right:40%;
        top:50%;
    }

    #tot_pane > div:nth-child(3) {
        left:40%;
        bottom: 25%;
    }

    #tot_timer_wrapper {
        display: none;
        text-align:center;
    }
    #tot_timer {
        font-size: 20px;
        font-weight:bold;
        text-align:center;
    }

    #tot_prize_wrapper {
        display: none;
        text-align:center;
    }

    #tot_prize_image {
        max-height:350px;
        vertical-align:middle;
    }

    #tot_prize_image_wrapper {
        height: 350px;
        vertical-align: middle;
        line-height: 350px;
    }
    div#no_script {
        position: fixed;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        background-color:black;
        color: white;
        z-index: 1;
    }
    div#no_script > div {
        margin: 200px auto;
        width: 500px;
        text-align:center;
    }
    </style>

    <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

        ga('create', 'UA-42197350-2', 'auto');
        ga('send', 'pageview');

    </script>
</head>

<body>
<div id="no_script">
    <div>Javascript <em>must</em> be enabled to use this site.<br /><br />
    <a href="http://enable-javascript.com/"><i class="fa fa-hand-pointer-o"></i>How to Enable Javascript</a></div>
</div>
<div id="loading_overlay">
    <div>
        <i class="fa fa-spinner fa-pulse fa-5x"></i> Logging in . . .
    </div>
</div>
<div id="fullpage">
    <div class="section first" data-anchor="entry">
        <a href="#main">
        <div id="infoBox">
            <i id="infoArrow" class="fa fa-angle-down"></i>
            <span id="infoText">Scroll Down to Enter</span>
        </div>
        </a>
    </div>
    <div class="section second" data-anchor="main">
        <?php echo show_top_menu($loggedIn, $icon, $username); ?>
        <div id="main_content">
            <ul class="txtrotate">
                <!-- Info -->
                <li>
                    <img class="header" src="/apps/assets/raffle/img/headers/info.gif" />
                    <h1>Welcome to Halloween at the Pillowing Pile</h1>
                    <hr />
                    We will be hosting a trick or treat style event where everyone can come check here once, maybe twice,
                    per day to get candy and win prizes! We have all sorts of goodies in store for you folks!<br /><br />
                    Click the <i class="fa fa-deviantart color-da-green"> </i> DeviantArt button to get started. You will be asked to approve some public access on your DA account.
                    We will need to use this information to identify you when redeeming prizes.<br /><br />
                    Once you have granted access to the app, scroll on down to the Trick-or-Treat page to get your candy or prize!<br /><br />
                    <strong>Trick or Treat will reset daily at 6am (USA Central Time) and if you're following the Pillowing-Pile DA group you will get another reset at 6pm!</strong>
                </li>
                <!-- End Info -->
                <!-- Help -->
                <li>
                    <img class="header" src="/apps/assets/raffle/img/headers/faq.png" />
                    <h1>Need Some Help?</h1>
                    If you have any problems or questions for us, you can check out the <a href="">Frequently Asked Questions event journal</a>!
                    <br /><br />
                    If you are experiencing errors with the website please comment on the journal and ping <a href="http://clovercoin.deviantart.com">CloverCoin</a> so she and Prov can check into the problem for you!
                    <br /><br />
                    We hope to make this a smooth and easy to understand experience for all our users. So please don’t be afraid to ask questions! We’ll do our best to help when we can.<br />

                </li>
                <!-- End Help -->
                <!-- Credits -->
                <li>
                    <img class="header" src="/apps/assets/raffle/img/headers/credits.png" />
                    <h1>Special Thanks</h1>
                    <hr />
                    <div id="credits">
                        <div class="row">
                            <div class="name"><a href="http://clovercoin.deviantart.com">AJ</a></div>
                            <div class="credit">Team Lead, Artwork, Design</div>
                        </div>
                        <div class="row">
                            <div class="name"><a href="http://provinite.deviantart.com">Prov</a></div>
                            <div class="credit">Planning, Programming, Design</div>
                        </div>
                        <div class="row">
                            <div class="name"><a href="http://maze.deviantart.com">Maze</a></div>
                            <div class="credit">Moderation, Artwork</div>
                        </div>
                        <div class="row">
                            <div class="name"><a href="http://coaste.deviantart.com">Coaste</a></div>
                            <div class="credit">Moderation, Artwork</div>
                        </div>
                        <div class="row">
                            <div class="name"><a href="http://omgproductions.deviantart.com">OMGProductions</a></div>
                            <div class="credit">Moderation, Artwork</div>
                        </div>
                        <div class="row">
                            <div class="name"><a href="http://coyoteluck.deviantart.com">CoyoteLuck</a></div>
                            <div class="credit">Moderation, Artwork</div>
                        </div>
                        <div class="row">
                            <div class="name"><a href="http://crayonkat.deviantart.com">CrayonKat</a></div>
                            <div class="credit">Moderation, Artwork</div>
                        </div>
                        <div class="row">
                            <div class="name"><a href="http://skippyskiddo.deviantart.com">SkippySkiddo</a></div>
                            <div class="credit">Moderation, Artwork</div>
                        </div>
                        <div class="row">
                            <div class="name"><a href="http://zingey.deviantart.com">Zingey</a></div>
                            <div class="credit">Moderation, Artwork</div>
                        </div>
                        <div class="row">
                            <div class="name"><a href="http://firekit11.deviantart.com">Firekit11</a></div>
                            <div class="credit">Moderation</div>
                        </div>
                        <div class="row">
                            <div class="name"><a href="http://flameshuken.deviantart.com">FlameShuken</a></div>
                            <div class="credit">Moderation</div>
                        </div>
                        <div class="row">
                            <div class="name"><a href="http://sailorcatbutt.deviantart.com">SailorCatButt</a></div>
                            <div class="credit">Moderation</div>
                        </div>
                        <div class="row">
                            <div class="name"><a href="http://your-undead.deviantart.com">Your-Undead</a></div>
                            <div class="credit">Moderation</div>
                        </div>
                        <br style="clear:both;" />
                    </div>
                </li>
                <!-- End Credits -->
                <?php if (!$loggedIn): ?>
                <!-- Log In -->
                <li>
                    <img class="header" src="/apps/assets/raffle/img/login.png" />
                    <h1>Ready to Get Started?</h1>
                    <hr />

                    <div style="text-align:center;">
                        <div class="da_button loginLink">
                            <img src="/apps/assets/raffle/img/login-with-da.png" class="loginLink" />
                        </div>
                    </div>
                    <div style="text-align:center;">
                        <i class="fa fa-hand-pointer-o clickhere"></i>
                        <strong><span class="clickhere">Click</span></strong>
                    </div>
                </li>
                <!-- End Log In -->
                <?php else: ?>
                <!-- Trick or Treat -->
                <li>
                    <div id="tot_pane_wrapper">
                        <h1>Trick or Treat!</h1>
                        <hr />
                        <p>
                            This must be the door where we go Trick or Treating. Well go on, click and give it a knock. <br />There is bound be something good here.
                        </p>
                        <div id="tot_pane">
                            <div>Knock!</div>
                            <div>Knock!</div>
                            <div>Knock!</div>
                        </div>
                    </div>
                    <div id="tot_timer_wrapper">
                        <div class="header"> </div>
                        <h1>Not Yet!</h1>
                        <hr />
                        Trick or Treat will reset again in<br /><br />
                        <div id="tot_timer"></div>
                    </div>
                    <div id="tot_prize_wrapper">
                        <h1 id="tot_prize_name"></h1>
                        <hr />
                        <div id="tot_prize_image_wrapper">
                            <img id="tot_prize_image" src=""/>
                        </div>
                        <hr />
                        <div id="tot_prize_desc"></div>
                    </div>
                </li>
                <!-- End Trick or Treat -->
                <?php endif; ?>
                <!-- Prizes -->
                <li>
                    <img class="header" src="/apps/assets/raffle/img/stats.png" />
                    <h1>Your Prizes</h1>
                    <hr />
                    Here you can see your ToT history!
                </li>
                <!-- End Prizes -->
            </ul>
            <div class="buttons">
                <div><i class="fa fa-info txtBtn txtBtnActive" data-idx="0"></i><span class="label">Info</span></div>
                <div><i class="fa fa-question-circle txtBtn" data-idx="1"></i><span class="label">Help</span></div>
                <div>
                <?php
                if (!$loggedIn)
                    echo '<i class="fa fa-deviantart txtBtn" data-idx="3"></i><span class="label">Log In</span>';
                else
                    echo '<i class="fa fa-gift txtBtn" data-idx="3"></i><span class="label">Trick or Treat</span>';
                ?>
                </div>
                <div><i class="fa fa-heart txtBtn" data-idx="2"></i><span class="label">Credits</span></div>
            </div>
        </div>
    </div>
</div>
</body>

</html>