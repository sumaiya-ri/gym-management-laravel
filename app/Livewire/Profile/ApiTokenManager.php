<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use Illuminate\Support\Facades\Log;

class ApiTokenManager extends Component
{
    /**
     * The token name.
     */
    public $name = '';

    /**
     * The selected abilities for the new token.
     */
    public $selectedAbilities = [];

    /**
     * The expiration selection for the new token (in days).
     */
    public $expiresInDays = '30';

    /**
     * The plain text token being displayed to the user after creation.
     */
    public $displayToken = null;

    /**
     * The token ID being confirmed for revocation.
     */
    public $tokenIdBeingRemoved = null;

    /**
     * Render the Livewire component.
     */
    public function render()
    {
        return view('livewire.profile.api-token-manager', [
            'tokens' => auth()->user()->tokens()->latest()->get(),
            'availableAbilities' => auth()->user()->getAbilitiesByRole(),
        ]);
    }

    /**
     * Create a new API token.
     */
    public function createToken()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'expiresInDays' => 'required|in:7,30,90,never',
        ]);

        $user = auth()->user();
        $allowedAbilities = $user->getAbilitiesByRole();
        
        // Filter and intersect to make sure user cannot assign abilities outside their role
        $abilities = array_intersect(array_keys(array_filter($this->selectedAbilities)), $allowedAbilities);
        
        // If no abilities selected, default to all allowed abilities for the role
        if (empty($abilities)) {
            $abilities = $allowedAbilities;
        }

        $expiresAt = match ($this->expiresInDays) {
            '7' => now()->addDays(7),
            '30' => now()->addDays(30),
            '90' => now()->addDays(90),
            default => null,
        };

        $tokenResult = $user->createToken($this->name, $abilities, $expiresAt);

        Log::info('Token created via Web UI.', [
            'user_id' => $user->id,
            'token_name' => $this->name,
            'abilities' => $abilities,
            'expires_at' => $expiresAt ? $expiresAt->toDateTimeString() : 'Never',
        ]);

        // Save token to display once
        $this->displayToken = $tokenResult->plainTextToken;

        // Reset inputs
        $this->name = '';
        $this->selectedAbilities = [];
        $this->expiresInDays = '30';

        session()->flash('status', 'API Token created successfully! Please copy it now as it will not be shown again.');
    }

    /**
     * Show confirmation modal for token revocation.
     */
    public function confirmTokenRevocation($tokenId)
    {
        $this->tokenIdBeingRemoved = $tokenId;
    }

    /**
     * Revoke the selected API token.
     */
    public function revokeToken()
    {
        if ($this->tokenIdBeingRemoved) {
            $token = auth()->user()->tokens()->find($this->tokenIdBeingRemoved);

            if ($token) {
                $token->delete();

                Log::info('Token revoked via Web UI.', [
                    'user_id' => auth()->id(),
                    'token_id' => $this->tokenIdBeingRemoved,
                ]);

                session()->flash('status', 'API Token revoked successfully.');
            }
        }

        $this->tokenIdBeingRemoved = null;
    }

    /**
     * Close the display token modal.
     */
    public function closeTokenModal()
    {
        $this->displayToken = null;
    }

    /**
     * Close the revocation modal.
     */
    public function cancelRevocation()
    {
        $this->tokenIdBeingRemoved = null;
    }
}
