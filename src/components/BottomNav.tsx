import { NavLink } from "react-router-dom";
import { Icon } from "./Icon";
import { cn } from "@/lib/utils";

const items = [
  { to: "/scan", icon: "qr_code_scanner", label: "Redeem" },
  { to: "/store", icon: "library_music", label: "Library" },
  { to: "/profile", icon: "person", label: "Profile" },
];

export function BottomNav() {
  return (
    <nav className="fixed bottom-0 left-0 right-0 z-50 bg-surface-container-lowest flex justify-around items-center pt-sm pb-xl px-container-margin">
      {items.map((item) => (
        <NavLink
          key={item.to}
          to={item.to}
          className={({ isActive }) =>
            cn(
              "flex flex-col items-center justify-center transition-colors duration-200 active:scale-110",
              isActive ? "text-primary" : "text-secondary hover:text-on-surface"
            )
          }
        >
          {({ isActive }) => (
            <>
              <Icon name={item.icon} filled={isActive} />
              <span className="font-label-md text-label-md">{item.label}</span>
            </>
          )}
        </NavLink>
      ))}
    </nav>
  );
}
