<!DOCTYPE html>
<html class="no-js" lang="<?=$lang?>">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?=$title . $title_append?></title>
        <?php if(isset($favicon)): ?><link rel='icon' href='<?=$this->url->asset($favicon)?>'/><?php endif; ?>
        <?php foreach($stylesheets as $stylesheet): ?>
        <link rel='stylesheet' type='text/css' href='<?=$this->url->asset($stylesheet)?>'/>
        <?php endforeach; ?>
        <?php if(isset($style)): ?><style><?=$style?></style><?php endif; ?>
        <script src='<?=$this->url->asset($modernizr)?>'></script>
    </head>
    <body>
        <div id='wrapper'>

            <div id='header'>
            <?php if(isset($header)) echo $header?>
            <?php $this->views->render('header')?>
                <?php if ($this->views->hasContent('status')) : ?>
                <div id='status'>
                <?php $this->views->render('status')?>
                </div>
                <?php endif; ?>
            </div>

            <?php if ($this->views->hasContent('navbar')) : ?>
            <div id='navbar'>
            <?php $this->views->render('navbar')?>
            </div>
            <?php endif; ?>


            <div id='main'>
            <?php if(isset($main)) echo $main?>
            <?php $this->views->render('main')?>
            </div>


            <div id='footer'>
            <?php if(isset($footer)) echo $footer?>
            <?php $this->views->render('footer')?>
            </div>

        </div>

        <?php if(isset($jquery)):?><script src='<?=$this->url->asset($jquery)?>'></script><?php endif; ?>

        <?php if(isset($javascript_include)): foreach($javascript_include as $val): ?>
        <script src='<?=$this->url->asset($val)?>'></script>
        <?php endforeach; endif; ?>

    </body>
</html>
