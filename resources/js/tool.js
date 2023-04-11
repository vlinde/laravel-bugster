import LogFile from "./components/LogFile";
import Directory from "./components/Directory";
import StatusCodesChart from "./components/StatusCodesChart";

Nova.booting((Vue, router, store) => {
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
        {
            name: 'bugster-status-codes-chart',
            path: '/status-codes-chart',
            component: StatusCodesChart,
        },
    ])
});
