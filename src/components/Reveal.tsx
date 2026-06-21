import { motion, useReducedMotion, type Variants } from "framer-motion";
import type { ReactNode } from "react";

type Direction = "up" | "down" | "left" | "right" | "fade" | "scale";

type RevealProps = {
  children: ReactNode;
  direction?: Direction;
  delay?: number;
  duration?: number;
  distance?: number;
  className?: string;
  once?: boolean;
  amount?: number;
  as?: "div" | "section" | "article" | "header" | "footer" | "li";
};

const buildVariants = (
  direction: Direction,
  distance: number,
  duration: number,
  delay: number
): Variants => {
  const hidden: Record<string, number> = { opacity: 0 };
  if (direction === "up") hidden.y = distance;
  if (direction === "down") hidden.y = -distance;
  if (direction === "left") hidden.x = distance;
  if (direction === "right") hidden.x = -distance;
  if (direction === "scale") hidden.scale = 0.92;

  return {
    hidden,
    visible: {
      opacity: 1,
      x: 0,
      y: 0,
      scale: 1,
      transition: {
        duration,
        delay,
        ease: [0.22, 1, 0.36, 1],
      },
    },
  };
};

export function Reveal({
  children,
  direction = "up",
  delay = 0,
  duration = 0.7,
  distance = 32,
  className,
  once = true,
  amount = 0.2,
  as = "div",
}: RevealProps) {
  const reduce = useReducedMotion();
  const MotionTag = motion[as] as typeof motion.div;

  if (reduce) {
    return <MotionTag className={className}>{children}</MotionTag>;
  }

  return (
    <MotionTag
      className={className}
      initial="hidden"
      whileInView="visible"
      viewport={{ once, amount }}
      variants={buildVariants(direction, distance, duration, delay)}
    >
      {children}
    </MotionTag>
  );
}

type StaggerProps = {
  children: ReactNode;
  className?: string;
  stagger?: number;
  delay?: number;
  once?: boolean;
  amount?: number;
};

export function StaggerGroup({
  children,
  className,
  stagger = 0.08,
  delay = 0,
  once = true,
  amount = 0.15,
}: StaggerProps) {
  const reduce = useReducedMotion();
  if (reduce) return <div className={className}>{children}</div>;
  return (
    <motion.div
      className={className}
      initial="hidden"
      whileInView="visible"
      viewport={{ once, amount }}
      variants={{
        hidden: {},
        visible: { transition: { staggerChildren: stagger, delayChildren: delay } },
      }}
    >
      {children}
    </motion.div>
  );
}

export function StaggerItem({
  children,
  className,
  direction = "up",
  distance = 24,
}: {
  children: ReactNode;
  className?: string;
  direction?: Direction;
  distance?: number;
}) {
  const reduce = useReducedMotion();
  if (reduce) return <div className={className}>{children}</div>;
  const hidden: Record<string, number> = { opacity: 0 };
  if (direction === "up") hidden.y = distance;
  if (direction === "down") hidden.y = -distance;
  if (direction === "left") hidden.x = distance;
  if (direction === "right") hidden.x = -distance;
  if (direction === "scale") hidden.scale = 0.94;
  return (
    <motion.div
      className={className}
      variants={{
        hidden,
        visible: {
          opacity: 1,
          x: 0,
          y: 0,
          scale: 1,
          transition: { duration: 0.6, ease: [0.22, 1, 0.36, 1] },
        },
      }}
    >
      {children}
    </motion.div>
  );
}
