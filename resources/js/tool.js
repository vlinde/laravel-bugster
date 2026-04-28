import Logs from "./pages/Logs";
import StatusCodesChart from "./pages/StatusCodesChart";

Nova.inertia("BugsterLogs", Logs);
Nova.inertia("BugsterStatusCodesChart", StatusCodesChart);

Nova.booting((app, store) => {
  //
});
