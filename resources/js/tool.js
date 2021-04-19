import CustomIndexToolbar from "./components/CustomIndexToolbar";
import BugsterDBsIndexToolbar from "./components/BugsterDBsIndexToolbar";
import BugsterStatsIndexToolbar from "./components/BugsterStatsIndexToolbar";
Nova.booting((Vue, router, store) => {
    Vue.component('custom-index-toolbar', CustomIndexToolbar);
    Vue.component('advanced-bugster-d-bs-index-toolbar', BugsterDBsIndexToolbar);
    Vue.component('advanced-bugster-stats-index-toolbar', BugsterStatsIndexToolbar);
});