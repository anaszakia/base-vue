export const AVAILABLE_ICONS = [
    // General / Dashboard
    'LayoutGrid',
    'LayoutDashboard',
    'Home',
    'Gauge',
    'Compass',
    
    // User & Security
    'Users',
    'User',
    'UserCheck',
    'UserPlus',
    'UserCog',
    'Shield',
    'ShieldCheck',
    'ShieldAlert',
    'Key',
    'Lock',
    'Unlock',

    // Content & Academic / Data
    'BookOpen',
    'Book',
    'GraduationCap',
    'School',
    'Library',
    'FileText',
    'FileSpreadsheet',
    'Files',
    'Folder',
    'FolderTree',
    'FolderGit2',
    'Layers',
    'Database',
    'Table',
    'ClipboardList',
    'ListChecks',

    // Business & Commerce
    'Building',
    'Building2',
    'Briefcase',
    'CreditCard',
    'Wallet',
    'DollarSign',
    'Receipt',
    'ShoppingCart',
    'ShoppingBag',
    'Package',
    'Boxes',
    'Truck',

    // Communication & Calendar
    'Bell',
    'Mail',
    'MessageSquare',
    'Calendar',
    'Clock',
    'Send',

    // Analytics & Reports
    'BarChart3',
    'LineChart',
    'PieChart',
    'TrendingUp',
    'Activity',

    // System & Tools
    'Settings',
    'Sliders',
    'Menu',
    'Cpu',
    'HardDrive',
    'Server',
    'Globe',
    'Link',
    'Tag',
    'Bookmark',
    'HelpCircle',
    'Info',
] as const;

export type AvailableIcon = (typeof AVAILABLE_ICONS)[number];
