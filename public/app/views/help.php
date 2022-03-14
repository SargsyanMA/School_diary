<?php

if ($page == 'homework') {
    echo View::getInstance()->renderTwig('help-homework.html.twig', []);
}
elseif ($page == 'auth') {
    echo View::getInstance()->renderTwig('help-auth.html.twig', []);
}
else {
    echo View::getInstance()->renderTwig('help-index.html.twig', []);
}