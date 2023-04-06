import CustomIndexToolbar from "./components/CustomIndexToolbar";
import BugsterDBsIndexToolbar from "./components/BugsterDBsIndexToolbar";
import BugsterStatsIndexToolbar from "./components/BugsterStatsIndexToolbar";
import LogFile from "./components/LogFile";
import Directory from "./components/Directory";

Nova.booting((Vue, router, store) => {
    Vue.component('custom-index-toolbar', CustomIndexToolbar);
    Vue.component('advanced-bugster-d-bs-index-toolbar', BugsterDBsIndexToolbar);
    Vue.component('advanced-bugster-stats-index-toolbar', BugsterStatsIndexToolbar);

    router.addRoutes([
        {
            name: 'bugster-log-files',
            path: '/log-files',
            component: LogFile,
        },
        {
            name: 'bugster-log-files-directory',
            path: '/log-files/directory/:directory_path/:directory_name',
            component: Directory,
        },
    ])
});
