# Symfony Orion View

## usage

```php

<?php

namespace App\Controller;

use Orion\OrionEngine\Controllers\RenderOrionView;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AppController extends RenderOrionView
{
    #[Route('/app', name: 'home')]
    public function home(): Response{
        $a = 1;
        $b = 2;
        
        return $this->view("index",[
            "a" =>$a,
            "b" =>$b
        ]);
    }
}

```