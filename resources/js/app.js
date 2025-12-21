import "./bootstrap";

// Alpine.js Collapse Plugin
// Livewire includes Alpine.js automatically, but we need to register plugins
import Alpine from "alpinejs";
import collapse from "@alpinejs/collapse";

// Register collapse plugin
Alpine.plugin(collapse);

// Make Alpine available globally (Livewire will use this instance)
window.Alpine = Alpine;

// Start Alpine (Livewire will also start it, but this ensures it's ready)
// Note: Livewire 3 automatically starts Alpine, so we don't need to call Alpine.start() here
// But we ensure Alpine is available for any manual usage
if (!window.Alpine) {
  window.Alpine = Alpine;
}
