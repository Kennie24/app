import { Link } from "react-router-dom";
import { Icon } from "./Icon";

export function TopBar() {
  return (
    <header className="fixed top-0 left-0 right-0 z-50 flex justify-between items-center px-lg py-md bg-background/70 backdrop-blur-xl">
      <Link to="/scan" className="flex items-center gap-sm hover:opacity-90 transition-opacity">
        <Icon name="graphic_eq" className="text-primary text-headline-md" filled />
        <span className="font-headline-md text-headline-md font-black text-primary tracking-tighter">
          SoundRedeem
        </span>
      </Link>
      <div className="flex items-center gap-md">
        <Link
          to="/profile"
          aria-label="Open profile"
          className="flex h-9 w-9 items-center justify-center rounded-full border border-outline-variant bg-surface-container-high text-secondary hover:ring-2 hover:ring-primary/40 transition-all"
        >
          <Icon name="person" className="text-[20px]" />
        </Link>
      </div>
    </header>
  );
}
