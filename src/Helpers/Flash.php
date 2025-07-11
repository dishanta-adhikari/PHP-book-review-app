<?php

namespace App\Helpers;

class Flash
{
    public static function render(): void
    {
        if (isset($_SESSION['success']) || isset($_SESSION['error'])) {
            echo <<<SVG
            <svg xmlns="http://www.w3.org/2000/svg" class="d-none">
                <symbol id="check-circle-fill" viewBox="0 0 16 16">
                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
                </symbol>
            </svg>
            SVG;
        }

        if (isset($_SESSION['success'])) {
            echo '<div class="alert alert-success d-flex align-items-center alert-dismissible fade show container mt-3" role="alert">';
            echo '<svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Success:"><use xlink:href="#check-circle-fill" /></svg>';
            echo '<div>' . htmlspecialchars($_SESSION['success']) . '</div>';
            echo '<button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>';
            echo '</div>';
            unset($_SESSION['success']);
        } elseif (isset($_SESSION['error'])) {
            echo '<div class="alert alert-danger alert-dismissible fade show container mt-3" role="alert">';
            echo htmlspecialchars($_SESSION['error']);
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            echo '</div>';
            unset($_SESSION['error']);
        }
    }
}
