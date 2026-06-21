import { Outlet, useLocation } from "react-router-dom";
import { AnimatePresence } from "framer-motion";
import { useEffect } from "react";
import { TopBar } from "./TopBar";
import { BottomNav } from "./BottomNav";
import { ScrollProgress } from "./ScrollProgress";
import { PageTransition } from "./PageTransition";

export function AppShell() {
  const location = useLocation();

  // Scroll to top on route change so page-enter animations play cleanly.
  useEffect(() => {
    window.scrollTo({ top: 0, behavior: "instant" as ScrollBehavior });
  }, [location.pathname]);

  return (
    <div className="min-h-screen bg-background text-on-background">
      <ScrollProgress />
      <TopBar />
      <AnimatePresence mode="wait">
        <PageTransition key={location.pathname}>
          <Outlet />
        </PageTransition>
      </AnimatePresence>
      <BottomNav />
    </div>
  );
}
