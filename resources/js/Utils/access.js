export const normalizeAccessList = (value) => {
    if (!Array.isArray(value)) {
        return [];
    }

    return value
        .map((item) => String(item).trim().toLowerCase())
        .filter(Boolean);
};

export const canAccess = (user, access = {}) => {
    const roles = normalizeAccessList(user?.roles);
    const permissions = normalizeAccessList(user?.permissions);
    const requiredRoles = normalizeAccessList(access.roles);
    const blockedRoles = normalizeAccessList(access.excludeRoles);
    const requiredPermissions = normalizeAccessList(access.permissions);
    const blockedPermissions = normalizeAccessList(access.excludePermissions);
    const supplierStatus = String(user?.supplier?.status || '').toLowerCase();
    const requiresSupplierApproval = Boolean(access.requiresSupplierApproval);

    if (requiredRoles.length > 0 && !requiredRoles.some((role) => roles.includes(role))) {
        return false;
    }

    if (blockedRoles.some((role) => roles.includes(role))) {
        return false;
    }

    if (requiredPermissions.length > 0 && !requiredPermissions.some((permission) => permissions.includes(permission))) {
        return false;
    }

    if (blockedPermissions.some((permission) => permissions.includes(permission))) {
        return false;
    }

    if (requiresSupplierApproval && supplierStatus !== 'approved' && !roles.includes('admin')) {
        return false;
    }

    return true;
};

