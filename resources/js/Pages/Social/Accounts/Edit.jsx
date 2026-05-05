import AccountForm from './Form';

export default function Edit({ account, ...props }) {
    return <AccountForm {...props} account={account} submitUrl={route('social.accounts.update', account.id)} submitMethod="put" />;
}
