// 21st.dev / Stagewise toolbar — only active in dev.
// Lets you select React components in the browser and ship them to your AI editor.
import { StagewiseToolbar } from "@stagewise/toolbar-react";

export function StagewiseDevToolbar() {
  if (!import.meta.env.DEV) return null;
  return (
    <StagewiseToolbar
      config={{
        plugins: [],
      }}
    />
  );
}
