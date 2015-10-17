<!DOCTYPE html>
<html>
<head>
    <title>CloverCoin - Raffle</title>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/fullPage.js/2.7.4/jquery.fullPage.js"></script>

    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/fullPage.js/2.7.4/jquery.fullPage.css" />
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <link href='//fonts.googleapis.com/css?family=Roboto:400,300' rel='stylesheet' type='text/css'>
    <script type="text/javascript">

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
                el.children().first().html(el.data('tr-htmls')[el.data('tr-idx')]);
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
                                htmls.push($(e).html());
                            });

                            el.data('tr-idx', 0);
                            el.data('tr-maxidx', htmls.length);
                            el.data('tr-htmls', htmls);

                            el.children().remove();
                            el.append('<div></div>');

                            goto(el, 0, props);

                            console.log(htmls);
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

        $(document).ready(function() {
            $('#fullpage').fullpage({
                afterLoad: function() {
                    $('body').trigger('resize');
                }
            });
            var textRotator = $('.txtrotate');

            $(textRotator).textRotate({
                outTime: 500,
                inTime: 500
            });

            $('.txtBtn').click(function() {
                if ($(this).hasClass('txtBtnActive')) { return; }
                $('.txtBtn').removeClass('txtBtnActive');
                var idx = $(this).data('idx');
                $(textRotator).textRotate(idx);
                $(this).addClass('txtBtnActive');
            });
        });
    </script>
    <style type="text/css">
        a {
            text-decoration:none;
        }
        body {
            font-family: "Roboto", "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif;
        }
        #fullpage div.section.first {
            background: #252B2D url('/apps/assets/raffle/img/largebg_1920_1080.png') top center/contain no-repeat;
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
            width:500px;
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
        }

        .txtBtn {
            font-size:30pt;
            padding:0 20px;
        }

        .txtBtn:not(.txtBtnActive) {
            cursor: pointer;
        }

        .txtBtn.txtBtnActive {
            color:#E87952;
        }

        .txtBtn:hover {
            color:#E87952;
        }

        .txtBtn.fa-deviantart:hover {
            color: #05CC47;
        }

        .txtBtn.fa-deviantart.txtBtnActive {
            color: #05CC47;
        }

        .txtrotate {
            max-width:800px;
            margin:0 auto;
            text-align:justify;
            padding-left:40px;
            padding-right:40px;
            min-height:300px;
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
            margin:0 auto;
            box-sizing:border-box;
            padding-top:5px;
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
        div#credits div.row div.credit {
            float:left;
            width: 250px;
        }

        a {
            color: #E87952;
        }
    </style>
</head>

<body>
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
        <div id="main_content">
            <ul class="txtrotate">
                <!-- Info -->
                <li>
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
                    <h1>Need Some Help?</h1>
                    <hr />
                    If you have any questions about Pillowings or the Trick-or-Treat event, head on over to the <a href="#">Help Discussion</a>.
                    <br /><br />
                    If you're having issues with the site, please contact <a href="http://clovercoin.deviantart.com">CloverCoin @ DA</a>.
                </li>
                <!-- End Help -->
                <!-- Credits -->
                <li>
                    <h1>Special Thanks</h1>
                    <hr />
                    <div id="credits">
                        <div class="row">
                            <div class="name">AJ</div>
                            <div class="credit">Team Lead, Artwork, Design</div>
                        </div>
                        <div class="row">
                            <div class="name">Prov</div>
                            <div class="credit">Programming, Design</div>
                        </div>
                        <div class="row">
                            <div class="name">SailorCatButt</div>
                            <div class="credit">Moderation, Artwork</div>
                        </div>
                        <br style="clear:both;" />
                    </div>
                </li>
                <!-- End Credits -->
                <!-- Log In -->
                <li>
                    <h1>Ready to Get Started?</h1>
                    <hr />

                    <div style="text-align:center;">
                        <div class="da_button">
                            <img src="/apps/assets/raffle/img/login-with-da.png" />
                        </div>
                    </div>
                    <div style="text-align:center;">
                        <i class="fa fa-hand-pointer-o clickhere"></i>
                        <strong><span class="clickhere">Click</span></strong>
                    </div>
                </li>
                <!-- End Log In -->
            </ul>
            <div class="buttons">
                <i class="fa fa-info txtBtn txtBtnActive" data-idx="0"></i>
                <i class="fa fa-question-circle txtBtn" data-idx="1"></i>
                <i class="fa fa-deviantart txtBtn" data-idx="3"></i>
                <i class="fa fa-heart txtBtn" data-idx="2"></i>
            </div>
        </div>
    </div>
    <div class="section" data-anchor="page3">Some section</div>
    <div class="section" data-anchor="page4">Some section</div>
</div>
</body>

</html>