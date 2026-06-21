import { cn } from "@/lib/utils";
import type { CSSProperties } from "react";

type IconProps = {
  name: string;
  className?: string;
  filled?: boolean;
  style?: CSSProperties;
};

export function Icon({ name, className, filled, style }: IconProps) {
  return (
    <span
      className={cn("material-symbols-outlined", className)}
      style={{
        ...(filled ? { fontVariationSettings: "'FILL' 1" } : null),
        ...style,
      }}
    >
      {name}
    </span>
  );
}
