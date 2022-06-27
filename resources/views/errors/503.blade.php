<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Varela" rel="stylesheet">
    <link rel="stylesheet" href="//use.fontawesome.com/releases/v6.0.0/css/all.css">
    <title>Maintenance &bull; FyreNodes</title>
    <link rel="icon" href="/favicon/favicon.ico">
</head>
<body>
<div class="container">
    <div class="content">
        <div class="error-code">503</div>
        <br/><br/>
        <span class="info">Service Unavailable / Maintenance</span>
    </div>
    <div class="social">
        <ul class="social-list">
            <li><a href="/"><i class="fa-solid fa-house"></i></a></li>
            <li><a href="https://fyrenodes.com/discord"><i class="fa-brands fa-discord"></i></a></li>
            <li><a href="https://fyrenodes.com/twitter"><i class="fa-brands fa-twitter"></i></a></li>
            <li><a href="https://status.fyrenodes.com"><i class="fa-solid fa-signal"></i></a></li>
        </ul>
    </div>
</div>
</body>
<style>
    html,
    body,
    div,
    span,
    applet,
    object,
    iframe,
    h1,
    h2,
    h3,
    h4,
    h5,
    h6,
    p,
    blockquote,
    pre,
    a,
    abbr,
    acronym,
    address,
    big,
    cite,
    code,
    del,
    dfn,
    em,
    img,
    ins,
    kbd,
    q,
    s,
    samp,
    small,
    strike,
    strong,
    sub,
    sup,
    tt,
    var,
    b,
    u,
    i,
    center,
    dl,
    dt,
    dd,
    ol,
    ul,
    li,
    fieldset,
    form,
    label,
    legend,
    table,
    caption,
    tbody,
    tfoot,
    thead,
    tr,
    th,
    td,
    article,
    aside,
    canvas,
    details,
    embed,
    figure,
    figcaption,
    footer,
    header,
    hgroup,
    menu,
    nav,
    output,
    ruby,
    section,
    summary,
    time,
    mark,
    audio,
    video {
        margin: 0;
        padding: 0;
        border: 0;
        font: inherit;
        vertical-align: baseline;
    }


    /* HTML5 display-role reset for older browsers */

    article,
    aside,
    details,
    figcaption,
    figure,
    footer,
    header,
    hgroup,
    menu,
    nav,
    section,
    main {
        display: block;
    }

    body {
        line-height: 1;
    }

    ol,
    ul {
        list-style: none;
    }

    blockquote,
    q {
        quotes: none;
    }

    blockquote:before,
    blockquote:after,
    q:before,
    q:after {
        content: none;
    }

    table {
        border-collapse: collapse;
        border-spacing: 0;
    }

    html {
        background: radial-gradient(#000, #111);
        color: white;
        overflow: hidden;
        height: 100%;
        user-select: none;
    }

    .error-code {
        font-family: 'Varela', sans-serif;
        text-align: center;
        font-size: 95px;
        width: 110px;
        height: 60px;
        line-height: 60px;
        margin: auto;
        position: absolute;
        top: 0;
        bottom: 0;
        left: -60px;
        right: 0;
        animation: noise 2s linear infinite;
        overflow: initial;
    }

    .error-code:after {
        font-family: 'Varela', sans-serif;
        content: '503';
        font-size: 100px;
        text-align: center;
        width: 150px;
        height: 60px;
        line-height: 60px;
        margin: auto;
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        opacity: 0;
        color: blue;
        animation: noise-1 .2s linear infinite;
    }

    .info {
        text-align: center;
        font-size: 15px;
        font-style: italic;
        height: 60px;
        line-height: 60px;
        margin: auto;
        position: absolute;
        top: 140px;
        bottom: 0;
        left: 0;
        right: 0;
        animation: noise-3 1s linear infinite;
    }

    .error-code:before {
        font-family: 'Varela', sans-serif;
        content: '503';
        font-size: 100px;
        text-align: center;
        width: 100px;
        height: 60px;
        line-height: 60px;
        margin: auto;
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        opacity: 0;
        color: red;
        animation: noise-2 .2s linear infinite;
    }

    @keyframes noise-1 {
        0%,
        20%,
        40%,
        60%,
        70%,
        90% {
            opacity: 0;
        }
        10% {
            opacity: .1;
        }
        50% {
            opacity: .5;
            left: -6px;
        }
        80% {
            opacity: .3;
        }
        100% {
            opacity: .6;
            left: 2px;
        }
    }

    @keyframes noise-2 {
        0%,
        20%,
        40%,
        60%,
        70%,
        90% {
            opacity: 0;
        }
        10% {
            opacity: .1;
        }
        50% {
            opacity: .5;
            left: 6px;
        }
        80% {
            opacity: .3;
        }
        100% {
            opacity: .6;
            left: -2px;
        }
    }

    @keyframes noise {
        0%,
        3%,
        5%,
        42%,
        44%,
        100% {
            opacity: 1;
            transform: scaleY(1);
        }
        4.3% {
            opacity: 1;
            transform: scaleY(1.7);
        }
        43% {
            opacity: 1;
            transform: scaleX(1.5);
        }
    }

    @keyframes noise-3 {
        0%,
        3%,
        5%,
        42%,
        44%,
        100% {
            opacity: 1;
            transform: scaleY(1);
        }
        4.3% {
            opacity: 1;
            transform: scaleY(4);
        }
        43% {
            opacity: 1;
            transform: scaleX(10) rotate(60deg);
        }
    }

    .social {
        position: absolute;
        bottom: 15px;
        left: 15px;
    }

    .social-list {
        margin: 0;
        padding: 0;
        list-style-type: none;
    }

    .social-list li {
        display: inline-block;
        margin: 5px 10px;
    }

    .social-list li a {
        color: #ffffff;
    }

    @media (max-width: 480px) {
        .links a {
            margin: 10px;
            width: 280px;
        }
    }
</style>
</html>