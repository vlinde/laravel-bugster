import Logs from "./components/Logs";
import StatusCodesChart from "./components/StatusCodesChart";

Nova.booting((Vue, router, store) => {
    router.addRoutes([
        {
            name: 'bugster-log-files',
            path: '/log-files',
            component: Logs,
        },
        {
            name: 'bugster-status-codes-chart',
            path: '/status-codes-chart',
            component: StatusCodesChart,
        },
    ])
});
