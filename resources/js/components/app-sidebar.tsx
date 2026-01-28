import { NavUser } from '@/components/nav-user';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarGroup,
    SidebarGroupContent,
    SidebarGroupLabel,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarSeparator,
} from '@/components/ui/sidebar';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/react';
import { Archive, Folder, MessageCircle, UploadCloud } from 'lucide-react';
import AppLogo from './app-logo';

const backupNavItems: NavItem[] = [
    {
        title: 'Vaults',
        href: '/vaults',
        icon: Archive,
    },
    {
        title: 'All Backups',
        href: '/backups',
        icon: Folder,
    },
    {
        title: 'Upload Backups',
        href: '/backups/upload',
        icon: UploadCloud,
    },

    /*
    {
        title: 'Audit Logs',
        href: '/audits',
        icon: ShieldCheck,
    },
    {
        title: 'Share Backups',
        href: '/backups/share',
        icon: Share2,
    },
     */
];

/*
const accountNavItems: NavItem[] = [
    {
        title: 'Upgrade to Pro',
        href: '/upgrade',
        icon: Settings,
    },
    {
        title: 'Billing',
        href: '/billing',
        icon: CreditCard,
    },
];*/

const helpNavItems: NavItem[] = [
    // {
    //     title: 'Support',
    //     href: '/support',
    //     icon: LifeBuoy,
    // },
    {
        title: 'Feedback',
        href: '/feedback',
        icon: MessageCircle,
    },
];

export function AppSidebar() {
    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <AppLogo />
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <SidebarGroup className="px-2 py-0">
                    <SidebarGroupLabel>Backup Management</SidebarGroupLabel>
                    <SidebarGroupContent>
                        <SidebarMenu>
                            {backupNavItems.map((item) => (
                                <SidebarMenuItem key={item.title}>
                                    <SidebarMenuButton asChild>
                                        <Link href={item.href}>
                                            {item.icon && <item.icon />}
                                            <span>{item.title}</span>
                                        </Link>
                                    </SidebarMenuButton>
                                </SidebarMenuItem>
                            ))}
                        </SidebarMenu>
                    </SidebarGroupContent>
                </SidebarGroup>
            </SidebarContent>

            <SidebarFooter>
                <SidebarGroup className="px-2 py-0">
                    <SidebarGroupLabel>Help Center</SidebarGroupLabel>
                    <SidebarGroupContent>
                        <SidebarMenu>
                            {helpNavItems.map((item) => (
                                <SidebarMenuItem key={item.title}>
                                    <SidebarMenuButton asChild>
                                        <Link href={item.href}>
                                            {item.icon && <item.icon />}
                                            <span>{item.title}</span>
                                        </Link>
                                    </SidebarMenuButton>
                                </SidebarMenuItem>
                            ))}
                        </SidebarMenu>
                    </SidebarGroupContent>
                </SidebarGroup>
                <SidebarSeparator className="my-1" />
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
