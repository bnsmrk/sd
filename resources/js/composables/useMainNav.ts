import type { NavItem } from '@/types';
import { BookOpen, Briefcase, LayoutGrid, Users } from 'lucide-vue-next';

export function useNavigation(role: string = '') {
    const groupedNavItems: Record<string, NavItem[]> = {
        common: [],
        admin: [
            { title: 'Dashboard', href: '/dashboard', icon: LayoutGrid },
            { title: 'Year Levels', href: '/year-levels', icon: BookOpen },
            { title: 'Sections', href: '/sections', icon: LayoutGrid },
            { title: 'Subjects', href: '/subjects', icon: BookOpen },
            { title: 'Enroll Students', href: '/enroll', icon: Users },
            { title: 'Register User', href: '/users', icon: Users },
            { title: 'Teacher Assign', href: '/teacher-assignments', icon: Users },
            { title: 'Student Proficiency Report', href: '/principal-students-proficiency', icon: BookOpen },
            { title: 'Teachers Lesson Plans', href: '/principal-teachers-lesson-plans', icon: BookOpen },
        ],
        teacher: [
            { title: 'Dashboard', href: '/teacher-dashboard', icon: Briefcase },
            { title: 'Upload Materials', href: '/materials', icon: Briefcase },
            { title: 'Activities', href: '/activities', icon: Briefcase },
            { title: 'My Modules', href: '/modules', icon: Briefcase },
            { title: 'Student Proficiency', href: '/students-proficiency', icon: Briefcase },
        ],
        principal: [],
        student: [
            { title: 'Dashboard', href: '/student-dashboard', icon: Briefcase },
            { title: 'My Subjects', href: '/my-subjects', icon: BookOpen },
        ],
    };

    return [...groupedNavItems.common, ...(role && groupedNavItems[role] ? groupedNavItems[role] : [])];
}
