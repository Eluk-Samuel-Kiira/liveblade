<?php

namespace LiveBlade\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\View;

class LiveBladeController extends Controller
{
    /**
     * Refresh a LiveBlade component via AJAX
     *
     * @param Request $request
     * @param string $component
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function refresh(Request $request, $component)
    {
        // Check if this is an AJAX request
        if (!$request->ajax() && !$request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid request method. AJAX required.'
            ], 400);
        }
        
        // Get the blade file to reload from request
        $bladeFileToReload = $request->query('bladeFileToReload');
        
        // If specific component ID is provided
        if ($bladeFileToReload) {
            // Check if view exists
            if (View::exists($bladeFileToReload)) {
                return view($bladeFileToReload)->render();
            }
            
            // Check for partial view
            $partialView = "partials.{$bladeFileToReload}";
            if (View::exists($partialView)) {
                return view($partialView)->render();
            }
            
            // Return error if view not found
            return response()->json([
                'success' => false,
                'message' => "View '{$bladeFileToReload}' not found."
            ], 404);
        }
        
        // If no specific component, try to load the component view
        $componentView = "components.{$component}";
        
        if (View::exists($componentView)) {
            return view($componentView)->render();
        }
        
        // Fallback - try to load as a full view
        if (View::exists($component)) {
            return view($component)->render();
        }
        
        // Return error response
        return response()->json([
            'success' => false,
            'message' => "Component '{$component}' not found.",
            'available_views' => $this->getAvailableViews()
        ], 404);
    }
    
    /**
     * Get a list of available views for debugging
     *
     * @return array
     */
    protected function getAvailableViews()
    {
        $paths = View::getFinder()->getPaths();
        $views = [];
        
        foreach ($paths as $path) {
            if (is_dir($path)) {
                $files = glob($path . '/*.blade.php');
                foreach ($files as $file) {
                    $views[] = basename($file, '.blade.php');
                }
            }
        }
        
        return array_slice($views, 0, 20); // Return first 20 views
    }


    // <!-- Refresh on search -->
    // <script>
    // function refreshComponent(componentId) {
    //     fetch(`/liveblade/refresh/${componentId}?bladeFileToReload=${componentId}`, {
    //         headers: { 'X-Requested-With': 'XMLHttpRequest' }
    //     })
    //     .then(res => res.json())
    //     .then(data => {
    //         if (data.success) {
    //             document.getElementById(componentId).innerHTML = data.html;
    //         }
    //     });
    // }
    // </script>
}