<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>付款计划 | 切换账套</title>

        <!-- Styles -->
        <style>
            html, body {
                font-family: "Lato Regular", "Helvetica Neue", Helvetica, Arial, "Hiragino Sans GB", "Microsoft YaHei", sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
                background-color: #ebeced;
            }
            .hero{
                margin: auto;
                width:800px;
                height:500px;
                background: #ffffff;
                padding: 8em;
            }

            .content {
                text-align: center;
            }

            h1 {
                line-height: 1.8;
                font-size: 36px;
                margin-top: 20px;
                margin-bottom: 10px;
                font-family: inherit;
                font-weight: 500;
                color: inherit;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
            .home .container {
                padding: 0 20px;
                background-color:#fff;
            }

            a {
                color: #f4645f;
                text-decoration: none;
            }
            a.unlink{
                color:gray;
                background: #efefef;
            }
            a.unlink:hover{
                text-decoration: none;
            }
            a:hover, a:focus {
                color: #23527c;
                text-decoration: underline;
            }

            @media only screen and (min-width: 75em){
                .container {
                    width: 76rem;
                }
            }
            @media only screen and (min-width: 64em){
                .container {
                    width: 65rem;
                }
            }
            @media only screen and (min-width: 48em){
                .container {
                    width: 49rem;
                }
            }
            .container, .container-fluid {
                margin-right: auto;
                margin-left: auto;
            }
            .third {
                position: relative;
                min-height: 1px;
                margin-right: 15px;
                margin-left: 15px;
                flex-basis: 33.333333%;
            }
            .callouts {
                margin: 0 -15px 30px -15px;
                display: -webkit-box;
                display: flex;
            }
            .callout {
                display: block;
            }
            .callout.minimal {
                border: 2px solid #e5e5e5;
                border-radius: 2px;
                padding: 15px 30px 20px;
                -webkit-transition: all .2s ease;
                transition: all .2s ease;
            }
            .callout.minimal:hover {
                border-color: transparent;
                border-radius: 0;
                box-shadow: 0 2px 7px rgba(0, 0, 0, 0.2);
            }
            .callout.minimal .callout-head {
                display: -webkit-box;
                display: flex;
            }
            .callout.minimal .callout-title {
                color: #444;
                font-weight: 300;
                font-size: 26px;
                margin: 0 0 30px;
                padding-top: 10px;
                -webkit-box-flex: 1;
                flex: 1;
            }
            .callout.minimal p {
                -webkit-font-smoothing: antialiased;
                color: #444;
                font-size: 17px;
                line-height: 1.6;
                margin: 0;
            }
        </style>
    </head>
    <body>
        <section class="hero">
            <div class="container">
                    <div class="content">
                        <h1>{{config('admin.name')}}</h1>
                        <p>&nbsp;</p>
                    </div>
                    <div class="callouts">
                        @foreach($books as $book)
                            <a href="{{$book->url}}" class="callout minimal third">
                                <div class="callout-head">
                                    <div class="callout-title">{{$book->title}}</div>
                                    <div class="callout-icon"></div>
                                </div>
                                <p>登录入口</p>
                            </a>
                        @endforeach
                        <a class="callout minimal third unlink">
                            <div class="callout-head">
                                <div class="callout-title">创舰</div>
                                <div class="callout-icon">
                                </div>
                            </div>
                            <p>尚未开通</p>
                        </a>
                    </div>

                    <div class="footer">
                        <div class="buttons">
                            <a href="http://www.ranto.cn" target="_blank" class="btn btn-default">http://www.ranto.cn</a>
                        </div>
                    </div>
                </div>
        </section>
    </body>
</html>
