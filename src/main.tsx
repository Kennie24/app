import { StrictMode } from "react";
import { createRoot } from "react-dom/client";
import { HashRouter } from "react-router-dom";
import "./index.css";
import App from "./App.tsx";
import { StagewiseDevToolbar } from "./components/StagewiseDevToolbar.tsx";

createRoot(document.getElementById("root")!).render(
  <StrictMode>
    <HashRouter>
      <App />
      <StagewiseDevToolbar />
    </HashRouter>
  </StrictMode>
);
